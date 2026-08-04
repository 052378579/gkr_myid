import os
os.environ["OMP_NUM_THREADS"] = "1"
os.environ["OPENBLAS_NUM_THREADS"] = "1"
os.environ["MKL_NUM_THREADS"] = "1"
os.environ["VECLIB_MAXIMUM_THREADS"] = "1"
os.environ["NUMEXPR_NUM_THREADS"] = "1"
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
import socket

C_RESET = "\033[0m"
C_GREEN = "\033[92m"
C_YELLOW = "\033[93m"
C_CYAN = "\033[96m"
C_RED = "\033[91m"
C_BOLD = "\033[1m"

load_dotenv('/var/www/gkr_myid/.env')
BOT_TOKEN = os.getenv('BOT_TOKEN')
CHAT_ID = os.getenv('CHAT_ID')

def send_telegram_notification(kesimpulan):
    if not BOT_TOKEN or not CHAT_ID: return
    try:
        try:
            host_name = socket.gethostname()
            ip_addr   = socket.gethostbyname(host_name)
        except Exception:
            ip_addr   = "127.0.0.1"

        ci_env = os.getenv('CI_ENVIRONMENT', 'production').lower()
        if "192.168.1.4" in ip_addr or "10.147.17.40" in ip_addr or ci_env == "development":
            server_label = "DEV"
        else:
            server_label = "PROD"

        waktu = datetime.now().strftime('%d-%m-%Y %H:%M:%S')

        pesan = (
            f"🧠 <b>AI Trainer Selesai (HARD RESET)!</b>\n\n"
            f"🖥️ <b>Server:</b> {server_label}\n"
            f"📂 <b>Mode:</b> Latih Ulang Total\n"
            f"⏰ <b>Waktu:</b> {waktu} WIB\n\n"
            f"💾 {kesimpulan}"
        )

        url = f"https://api.telegram.org/bot{BOT_TOKEN}/sendMessage"
        payload = {'chat_id': CHAT_ID, 'parse_mode': 'HTML', 'text': pesan}
        requests.post(url, data=payload, timeout=10)
    except Exception as e:
        pass

BASE_DIR = "/var/www/FOTO"
SCRIPT_DIR = os.path.dirname(os.path.realpath(__file__))
KATALOG_PATH = os.path.join(SCRIPT_DIR, "gkr_katalog.json")
BUKU_CATATAN_PATH = os.path.join(SCRIPT_DIR, "buku_catatan_ai.json")
PRODUK_INDEX_PATH = os.path.join(SCRIPT_DIR, "produk.index")
MAPPING_JSON_PATH = os.path.join(SCRIPT_DIR, "mapping.json")

print(f"{C_CYAN}[1/3] Memuat katalog dari gkr_katalog.json...{C_RESET}")
if not os.path.exists(KATALOG_PATH):
    print(f"{C_RED}ERROR: gkr_katalog.json tidak ditemukan! Jalankan crawler CI4 terlebih dahulu.{C_RESET}")
    exit(1)

with open(KATALOG_PATH, 'r') as f:
    katalog_data = json.load(f)

torch.set_num_threads(1)
print(f"{C_CYAN}[2/3] Memuat engine AI MobileNetV3...{C_RESET}")
weights = models.MobileNet_V3_Small_Weights.DEFAULT
model = models.mobilenet_v3_small(weights=weights)
model.eval()
feature_extractor = torch.nn.Sequential(*list(model.children())[:-1])

transform = transforms.Compose([
    transforms.Resize((224, 224)),
    transforms.ToTensor(),
    transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
])

BATCH_SIZE = 16

vectors = []
mapping = {}
ledger_ids = []
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
        ledger_ids.append(batch_ids[i])

batch_tensors = []
batch_ids = []
batch_identifiers = []
processed_count = 0

for item in katalog_data:
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
            tensor = transform(image)
            batch_tensors.append(tensor)
            batch_ids.append(item_id)
            batch_identifiers.append(identifier)
        except Exception:
            pass
            
    processed_count += 1
    if len(batch_tensors) == BATCH_SIZE:
        process_batch(batch_tensors, batch_ids, batch_identifiers)
        print(f"{C_GREEN}[{processed_count}/{len(katalog_data)}]{C_RESET} Diekstrak...")
        batch_tensors = []
        batch_ids = []
        batch_identifiers = []
        gc.collect()

if batch_tensors:
    process_batch(batch_tensors, batch_ids, batch_identifiers)
    print(f"{C_GREEN}[{processed_count}/{len(katalog_data)}]{C_RESET} Diekstrak...")
    gc.collect()

if vectors:
    print(f"\n{C_CYAN}[3/3] Membangun Index FAISS (IndexIDMap)...{C_RESET}")
    vectors_np = np.array(vectors).astype('float32')
    ids_np = np.array(faiss_ids).astype('int64')
    dimension = int(vectors_np.shape[1])

    index_base = faiss.IndexFlatIP(dimension)
    index_faiss = faiss.IndexIDMap(index_base)
    index_faiss.add_with_ids(vectors_np, ids_np)

    faiss.write_index(index_faiss, PRODUK_INDEX_PATH)
    with open(MAPPING_JSON_PATH, "w") as f:
        json.dump(mapping, f)
    with open(BUKU_CATATAN_PATH, "w") as f:
        json.dump(ledger_ids, f)

    msg = f"{len(vectors)} dari {len(katalog_data)} gambar masuk ke indeks."
    print(f"\n{C_BOLD}{C_GREEN}=== HARD RESET BERHASIL: {msg} ==={C_RESET}")
    send_telegram_notification(msg)
    print(f"{C_YELLOW}Merestart layanan ai_scanner...{C_RESET}")
    os.system("systemctl restart ai_scanner.service")
else:
    print(f"{C_RED}ERROR: Tidak ada gambar valid!{C_RESET}")
