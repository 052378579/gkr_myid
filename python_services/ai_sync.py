import os
os.environ["OMP_NUM_THREADS"] = "1"
os.environ["OPENBLAS_NUM_THREADS"] = "1"
os.environ["MKL_NUM_THREADS"] = "1"
os.environ["VECLIB_MAXIMUM_THREADS"] = "1"
os.environ["NUMEXPR_NUM_THREADS"] = "1"
os.environ["TORCH_HOME"] = "/var/www/gkr_myid/writable/torch_cache"

import json
import re
import sys
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
import socket
import shutil

C_RESET = "\033[0m"
C_GREEN = "\033[92m"
C_YELLOW = "\033[93m"
C_CYAN = "\033[96m"
C_RED = "\033[91m"
C_BOLD = "\033[1m"

load_dotenv('/var/www/gkr_myid/.env')
BOT_TOKEN = os.getenv('BOT_TOKEN')
CHAT_ID = os.getenv('CHAT_ID')

def send_telegram_notification(kesimpulan, direktori):
    try:
        host_name = socket.gethostname()
        ip_addr   = socket.gethostbyname(host_name)
    except Exception:
        ip_addr   = "127.0.0.1"
    ci_env = os.getenv('CI_ENVIRONMENT', 'production').lower()
    server_label = "DEV" if "192.168.1.4" in ip_addr or "10.147.17.40" in ip_addr or ci_env == "development" else "PROD"
    waktu = datetime.now().strftime('%d-%m-%Y %H:%M:%S WIB')
    
    payload = {
        "event": "crawler_done",
        "server": server_label,
        "waktu": waktu,
        "direktori": direktori,
        "info": kesimpulan
    }
    try:
        # Webhook lokal tanpa otentikasi
        requests.post("http://10.147.17.40:5678/webhook/gracia_telegram", json=payload, timeout=5)
    except Exception:
        pass

target_dir = sys.argv[1] if len(sys.argv) > 1 else "/var/www/FOTO"
relative_target = target_dir.replace("/var/www/FOTO", "").strip("/")
if relative_target:
    relative_target = relative_target + "/"

BASE_DIR = "/var/www/FOTO"
SCRIPT_DIR = os.path.dirname(os.path.realpath(__file__))
KATALOG_PATH = "/var/www/gkr_myid/writable/uploads/gkr_katalog.json"
BUKU_CATATAN_PATH = os.path.join(SCRIPT_DIR, "buku_catatan_ai.json")
PRODUK_INDEX_PATH = os.path.join(SCRIPT_DIR, "produk.index")
MAPPING_JSON_PATH = os.path.join(SCRIPT_DIR, "mapping.json")

if not os.path.exists(KATALOG_PATH) or not os.path.exists(BUKU_CATATAN_PATH) or not os.path.exists(PRODUK_INDEX_PATH):
    print(f"{C_RED}ERROR: File ledger/index tidak lengkap. Harap jalankan ai_reset.py terlebih dahulu!{C_RESET}")
    exit(1)

with open(KATALOG_PATH, 'r') as f:
    katalog_data = json.load(f)
with open(BUKU_CATATAN_PATH, 'r') as f:
    ledger_ids = set(json.load(f))
with open(MAPPING_JSON_PATH, 'r') as f:
    mapping = json.load(f)

index_faiss = faiss.read_index(PRODUK_INDEX_PATH)

db_ids = set([int(item['id']) for item in katalog_data])
new_ids_all = db_ids - ledger_ids
removed_ids = ledger_ids - db_ids

# Memfilter new_ids berdasarkan direktori target
if relative_target:
    new_ids = {nid for nid in new_ids_all if any(int(item['id']) == nid and str(item.get('imageUrl', '')).startswith(relative_target) for item in katalog_data)}
else:
    new_ids = new_ids_all

if not new_ids and not removed_ids:
    print(f"{C_GREEN}Tidak ada perubahan data pada {target_dir}. Index sudah mutakhir.{C_RESET}")
    exit(0)

print(f"{C_CYAN}Analisis Diferensial: {len(new_ids)} Gambar Baru, {len(removed_ids)} Gambar Dihapus.{C_RESET}")

if removed_ids:
    print(f"{C_YELLOW}Menghapus {len(removed_ids)} gambar dari memori...{C_RESET}")
    ids_to_remove = np.array(list(removed_ids)).astype('int64')
    index_faiss.remove_ids(ids_to_remove)
    for rid in removed_ids:
        mapping.pop(str(rid), None)

if new_ids:
    torch.set_num_threads(1)
    weights = models.MobileNet_V3_Small_Weights.DEFAULT
    model = models.mobilenet_v3_small(weights=weights)
    model.eval()
    feature_extractor = torch.nn.Sequential(*list(model.children())[:-1])
    transform = transforms.Compose([
        transforms.Resize((224, 224)),
        transforms.ToTensor(),
        transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
    ])
    
    new_items = [item for item in katalog_data if int(item['id']) in new_ids]
    
    vectors = []
    faiss_ids = []
    
    def process_batch(tensors, batch_ids, batch_identifiers):
        if not tensors: return
        batch_input = torch.stack(tensors)
        with torch.no_grad():
            embeddings = feature_extractor(batch_input).flatten(start_dim=1).numpy()
        norms = np.linalg.norm(embeddings, axis=1, keepdims=True)
        norms[norms == 0] = 1e-10
        embeddings = embeddings / norms
        for i in range(len(embeddings)):
            vectors.append(embeddings[i])
            faiss_ids.append(batch_ids[i])
            mapping[str(batch_ids[i])] = batch_identifiers[i]
            
    batch_tensors, batch_ids, batch_identifiers = [], [], []
    for item in new_items:
        item_id = int(item['id'])
        image_url = item['imageUrl']
        full_path = os.path.join(BASE_DIR, image_url)
        file_name = os.path.basename(image_url)
        base_name = os.path.splitext(file_name)[0]
        if image_url.startswith('SWATCHES/'):
            identifier = f"SWATCH:{base_name}"
        else:
            base_name = re.sub(r'[ _-]*(depan|belakang|samping|perspektif|detail|b|c|d|e)$', '', base_name, flags=re.IGNORECASE)
            identifier = base_name.strip()
        
        if os.path.exists(full_path):
            try:
                image = Image.open(full_path).convert('RGB')
                image.load()
                batch_tensors.append(transform(image))
                batch_ids.append(item_id)
                batch_identifiers.append(identifier)
            except Exception: pass
            
        if len(batch_tensors) == 16:
            process_batch(batch_tensors, batch_ids, batch_identifiers)
            batch_tensors, batch_ids, batch_identifiers = [], [], []
            gc.collect()
            
    if batch_tensors:
        process_batch(batch_tensors, batch_ids, batch_identifiers)
        gc.collect()

    if vectors:
        vectors_np = np.array(vectors).astype('float32')
        ids_np = np.array(faiss_ids).astype('int64')
        index_faiss.add_with_ids(vectors_np, ids_np)

print(f"{C_CYAN}Menyimpan Index FAISS dan State Ledger...{C_RESET}")
faiss.write_index(index_faiss, PRODUK_INDEX_PATH)
with open(MAPPING_JSON_PATH, "w") as f:
    json.dump(mapping, f)

# Hitung buku catatan baru dengan hati-hati: buang removed_ids, tambahkan HANYA new_ids yang diproses
updated_ledger = (ledger_ids - removed_ids).union(new_ids)
with open(BUKU_CATATAN_PATH, "w") as f:
    json.dump(list(updated_ledger), f)

msg = f"Inkremental Berhasil: {len(new_ids)} Ditambahkan, {len(removed_ids)} Dihapus."
print(f"{C_BOLD}{C_GREEN}=== {msg} ==={C_RESET}")
send_telegram_notification(msg, target_dir)

try:
    shutil.copy(PRODUK_INDEX_PATH, "/mnt/sdcard/ai-scanner/produk.index")
    shutil.copy(MAPPING_JSON_PATH, "/mnt/sdcard/ai-scanner/mapping.json")
except Exception as e:
    print(f"{C_RED}Gagal menyalin index ke /mnt/sdcard/ai-scanner: {str(e)}{C_RESET}")

os.system("sudo systemctl restart ai_scanner.service")
