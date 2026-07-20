import os
# Batasi thread pustaka C tingkat rendah (BLAS, OMP) untuk mencegah Segfault di ARM
os.environ["OMP_NUM_THREADS"] = "1"
os.environ["OPENBLAS_NUM_THREADS"] = "1"
os.environ["MKL_NUM_THREADS"] = "1"
os.environ["VECLIB_MAXIMUM_THREADS"] = "1"
os.environ["NUMEXPR_NUM_THREADS"] = "1"

# Arahkan Cache PyTorch ke direktori Writable CodeIgniter 4 agar www-data memiliki hak tulis!
os.environ["TORCH_HOME"] = "/var/www/gkr_myid/writable/torch_cache"

import json
import re
import numpy as np
import torch
import torchvision.models as models
import torchvision.transforms as transforms
from PIL import Image
import faiss
import gc
import requests
from dotenv import load_dotenv
from datetime import datetime

# Konstanta Warna ANSI
C_RESET = "\033[0m"
C_GREEN = "\033[92m"
C_YELLOW = "\033[93m"
C_CYAN = "\033[96m"
C_RED = "\033[91m"
C_BOLD = "\033[1m"

# Load environment variables
load_dotenv('/var/www/gkr_myid/.env')
BOT_TOKEN = os.getenv('BOT_TOKEN')
CHAT_ID = os.getenv('CHAT_ID')

def send_telegram_notification(kesimpulan):
    if not BOT_TOKEN or not CHAT_ID:
        print(f"{C_RED}Peringatan: Kredensial Telegram (BOT_TOKEN/CHAT_ID) tidak ditemukan di .env{C_RESET}")
        return
    try:
        waktu = datetime.now().strftime('%d-%m-%Y %H:%M:%S')
        pesan = f"<b>AI Trainer Selesai!</b>\n\n<b>Target:</b> /var/www/FOTO\n<b>Waktu:</b> {waktu} WIB\n\n<b>Hasil Eksekusi:</b>\n{kesimpulan}"
        
        url = f"https://api.telegram.org/bot{BOT_TOKEN}/sendMessage"
        payload = {
            'chat_id': CHAT_ID,
            'parse_mode': 'HTML',
            'text': pesan
        }
        requests.post(url, data=payload, timeout=10)
    except Exception as e:
        print(f"{C_RED}Gagal mengirim notifikasi Telegram: {e}{C_RESET}")

# Base direktori (Real Path)
BASE_DIR = "/var/www/FOTO"
TARGET_DIRS = ["BUYER", "GRACIA", "SAMPLE GRACIA", "SWATCHES", "WEB"]

# Batasi PyTorch agar tidak menghabiskan RAM & CPU
torch.set_num_threads(1)

# 1. Muat Engine AI
print(f"{C_CYAN}[1/3] Memuat engine AI MobileNetV3...{C_RESET}")
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
        image.load() # Paksa baca ke RAM untuk menangkap error korup (menghindari Bus Error)
        
        tensor = transform(image).unsqueeze(0)
        with torch.no_grad():
            embedding = feature_extractor(tensor).flatten().numpy()
        return embedding / np.linalg.norm(embedding)
    except:
        return None

vectors = []
mapping = {}

print(f"{C_CYAN}[2/3] Memproses direktori utama di: {C_YELLOW}{BASE_DIR}{C_RESET}")
all_files = []

for target in TARGET_DIRS:
    print(f"  {C_BOLD}->{C_RESET} Memindai folder: {C_YELLOW}{target}{C_RESET}")
    target_path = os.path.join(BASE_DIR, target)
    if os.path.exists(target_path):
        # Gunakan followlinks=True sebagai pelapis keamanan ganda
        for root, dirs, files in os.walk(target_path, followlinks=True):
            for file in files:
                if file.lower().endswith(('.jpg', '.jpeg', '.png', '.webp')):
                    all_files.append((target, os.path.join(root, file), file))

print(f"{C_GREEN}Menemukan {len(all_files)} file gambar.{C_RESET} Mulai ekstraksi fitur AI...")

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
        
    print(f"{C_GREEN}[{len(vectors)+1}/{len(all_files)}]{C_RESET} {C_BOLD}->{C_RESET} Ekstrak fitur: {C_YELLOW}{file_name}{C_RESET}")
        
    vector = extract_vector(full_path)
    if vector is not None:
        vectors.append(vector)
        mapping[str(len(vectors) - 1)] = identifier
        
    # Bersihkan memori secara manual
    gc.collect()

# 2. Bangun dan Ekspor Berkas Database Vektor
if vectors:
    print(f"\n{C_CYAN}[3/3] Membangun berkas database vektor FAISS...{C_RESET}")
    vectors_np = np.array(vectors).astype('float32')
    dimension = int(vectors_np.shape[1]) 
    
    index_faiss = faiss.IndexFlatIP(dimension)
    index_faiss.add(vectors_np)
    
    faiss.write_index(index_faiss, "produk.index")
    with open("mapping.json", "w") as f:
        json.dump(mapping, f)
        
    kesimpulan_msg = f"BERHASIL: {len(vectors)} dari {len(all_files)} gambar masuk ke indeks vektor!"
    print(f"\n{C_BOLD}{C_GREEN}=== SINKRONISASI {kesimpulan_msg} ==={C_RESET}")
    send_telegram_notification(kesimpulan_msg)
else:
    kesimpulan_msg = "ERROR: Tidak ada gambar valid yang diekstrak."
    print(f"\n{C_BOLD}{C_RED}{kesimpulan_msg}{C_RESET}")
    send_telegram_notification(kesimpulan_msg)
