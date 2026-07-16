import io
import json
import numpy as np
import torch
import torchvision.models as models
import torchvision.transforms as transforms
import subprocess
from fastapi import FastAPI, UploadFile, File
from fastapi.responses import StreamingResponse
from PIL import Image
import faiss

app = FastAPI()

# Muat database FAISS dan file mapping lokal saat startup server
index_faiss = faiss.read_index("produk.index")
with open("mapping.json", "r") as f:
    mapping = json.load(f)

# Muat Model Ekstraksi AI
weights = models.MobileNet_V3_Small_Weights.DEFAULT
model = models.mobilenet_v3_small(weights=weights)
model.eval()
feature_extractor = torch.nn.Sequential(*list(model.children())[:-1])

transform = transforms.Compose([
    transforms.Resize((224, 224)),
    transforms.ToTensor(),
    transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
])

@app.post("/scan")
async def scan_produk(file: UploadFile = File(...)):
    try:
        image_bytes = await file.read()
        image = Image.open(io.BytesIO(image_bytes)).convert('RGB')
        tensor = transform(image).unsqueeze(0)
        
        with torch.no_grad():
            user_vector = feature_extractor(tensor).flatten().numpy()
        user_vector = user_vector / np.linalg.norm(user_vector)
        user_vector_np = np.array([user_vector]).astype('float32')
        
        # Cari 15 foto terdekat (k=15)
        skor, indeks = index_faiss.search(user_vector_np, k=15)
        
        THRESHOLD = 0.68  # Batas kemiripan minimal 68%
        
        results = []
        seen_codes = set()
        
        # Iterasi dari hasil terbaik ke terburuk
        for i in range(15):
            conf = float(skor[0][i])
            idx_str = str(indeks[0][i])
            
            if conf >= THRESHOLD and idx_str in mapping:
                code = mapping[idx_str]
                if code not in seen_codes:
                    seen_codes.add(code)
                    results.append({
                        "kode_bom": code,
                        "confidence": conf
                    })
        
        if not results:
            return {"status": "not_found", "message": "Produk kursi tidak dikenali"}
            
        return {
            "status": "success",
            "results": results, # [{kode_bom: 'FG-1', confidence: 0.9}, ...]
            "kode_bom": results[0]['kode_bom'],  # Best match untuk backward compatibility
            "confidence": results[0]['confidence']
        }
    except Exception as e:
        return {"status": "error", "message": str(e)}

@app.post("/build_index")
async def build_index():
    def generate():
        yield "Mulai proses sinkronisasi AI Trainer...\n"
        try:
            # Jalankan script build_index.py dengan flag -u (unbuffered) agar output tidak ditahan
            process = subprocess.Popen(
                ["/mnt/sdcard/ai-scanner/env-ai/bin/python3", "-u", "build_index.py"],
                cwd="/mnt/sdcard/ai-scanner",
                stdout=subprocess.PIPE,
                stderr=subprocess.STDOUT,
                text=True,
                bufsize=1
            )
            
            # Stream output baris demi baris
            for line in iter(process.stdout.readline, ""):
                yield line
                
            process.stdout.close()
            process.wait()
            
            if process.returncode == 0:
                # Reload index secara otomatis setelah berhasil
                global index_faiss, mapping
                try:
                    index_faiss = faiss.read_index("produk.index")
                    with open("mapping.json", "r") as f:
                        mapping = json.load(f)
                    yield "\n=== OTOMATISASI: Index dan Mapping berhasil dimuat ulang ke memori API! ===\n"
                except Exception as e:
                    yield f"\n[ERROR] Gagal memuat ulang index: {str(e)}\n"
            else:
                yield f"\n[ERROR] Proses gagal dengan kode {process.returncode}\n"
                
        except Exception as e:
            yield f"\n[CRITICAL ERROR] Terjadi kegagalan sistem: {str(e)}\n"
            
    return StreamingResponse(generate(), media_type="text/plain")
