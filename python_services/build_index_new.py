import os
import json
import re
import numpy as np
import torch
import torchvision.models as models
import torchvision.transforms as transforms
from PIL import Image
import faiss

# Base direktori
BASE_DIR = "/var/www/gkr_myid/writable/FOTO"
TARGET_DIRS = ["BUYER", "GRACIA", "SAMPLE GRACIA", "SWATCHES"]
EXTRA_DIRS = ["/var/www/FOTO/WEB"]

# 1. Muat Engine AI
print("[1/3] Memuat engine AI MobileNetV3...")
weights = models.MobileNet_V3_Small_Weights.DEFAULT
model = models.mobilenet_v3_small(weights=weights)
model.eval()
feature_extractor = torch.nn.Sequential(*list(model.children())[:-1])

transform = transforms.Compose([
    transforms.Resize((224, 224)),
    transforms.ToTensor(),
    transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
])

def extract_vector(img_path):
    if not os.path.exists(img_path): return None
    try:
        image = Image.open(img_path).convert('RGB')
        tensor = transform(image).unsqueeze(0)
        with torch.no_grad():
            embedding = feature_extractor(tensor).flatten().numpy()
        return embedding / np.linalg.norm(embedding)
    except:
        return None

vectors = []
mapping = {}

print(f"[2/3] Memproses direktori utama di: {BASE_DIR} dan direktori tambahan")
all_files = []

for target in TARGET_DIRS:
    print(f"  -> Memindai folder: {target}")
    target_path = os.path.join(BASE_DIR, target)
    if os.path.exists(target_path):
        for root, dirs, files in os.walk(target_path):
            for file in files:
                if file.lower().endswith(('.jpg', '.jpeg', '.png', '.webp')):
                    all_files.append((target, os.path.join(root, file), file))

for extra_path in EXTRA_DIRS:
    print(f"  -> Memindai direktori ekstra: {extra_path}")
    if os.path.exists(extra_path):
        target = os.path.basename(extra_path)
        for root, dirs, files in os.walk(extra_path):
            for file in files:
                if file.lower().endswith(('.jpg', '.jpeg', '.png', '.webp')):
                    all_files.append((target, os.path.join(root, file), file))

print(f"Menemukan {len(all_files)} file gambar. Mulai ekstraksi fitur AI...")

for target_folder, full_path, file_name in all_files:
    identifier = None
    
    if target_folder == "SWATCHES":
        # Untuk Swatches, gunakan nama file tanpa ekstensi
        base_name = os.path.splitext(file_name)[0]
        identifier = f"SWATCH:{base_name}"
    else:
        # Gunakan nama file asli (tanpa ekstensi) secara universal
        base_name = os.path.splitext(file_name)[0]
        
        # Gunakan Regex untuk memotong kata/kode sudut di akhir string
        # Mendukung pemisah ganda (spasi+strip) dan variasi sudut B, C, D, E
        # Contoh: "_depan", " -D", "-E", " -B", " -C", "samping"
        base_name = re.sub(r'[ _-]*(depan|belakang|samping|perspektif|detail|b|c|d|e)$', '', base_name, flags=re.IGNORECASE)
        
        identifier = base_name.strip()

    if not identifier:
        continue
        
    vector = extract_vector(full_path)
    if vector is not None:
        vectors.append(vector)
        mapping[str(len(vectors) - 1)] = identifier
        
    if len(vectors) % 20 == 0 and len(vectors) > 0:
        print(f"  Berhasil mengekstrak {len(vectors)} vektor...")

# 2. Bangun dan Ekspor Berkas Database Vektor
if vectors:
    print("[3/3] Membangun berkas database vektor FAISS...")
    vectors_np = np.array(vectors).astype('float32')
    dimension = int(vectors_np.shape[1]) 
    
    index_faiss = faiss.IndexFlatIP(dimension)
    index_faiss.add(vectors_np)
    
    faiss.write_index(index_faiss, "produk.index")
    with open("mapping.json", "w") as f:
        json.dump(mapping, f)
        
    print(f"\n=== SINKRONISASI BERHASIL: {len(vectors)} dari {len(all_files)} gambar masuk ke indeks vektor! ===")
else:
    print("\nGagal: Tidak ada gambar valid yang diekstrak.")
