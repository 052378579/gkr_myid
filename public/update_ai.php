<?php
$pythonCode = <<<'PYTHON'
import io
import json
import numpy as np
import torch
import torchvision.models as models
import torchvision.transforms as transforms
from fastapi import FastAPI, UploadFile, File
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
        
        # Cari 1 foto terdekat (k=1)
        skor, indeks = index_faiss.search(user_vector_np, k=1)
        
        confidence = float(skor[0][0])
        idx_ditemukan = str(indeks[0][0])
        
        THRESHOLD = 0.68  # Batas kemiripan minimal 68%
        if confidence < THRESHOLD or idx_ditemukan not in mapping:
            return {"status": "not_found", "message": "Produk kursi tidak dikenali"}
            
        return {
            "status": "success",
            "kode_bom": mapping[idx_ditemukan],  # Mengembalikan string, misal: 'FG-24948' atau 'SWATCH:xxx'
            "confidence": confidence
        }
    except Exception as e:
        return {"status": "error", "message": str(e)}
PYTHON;

chdir('/mnt/sdcard/ai-scanner');
file_put_contents('main.py', $pythonCode);
echo "main.py updated successfully.";

