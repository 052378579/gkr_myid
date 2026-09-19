import os
import re
import sys
import logging
from urllib.request import urlopen, Request
from urllib.parse import unquote

# ---------------- CONFIGURATION ----------------
WINDOWS_SERVER_URL = "http://10.147.17.20/"
TARGET_DIR = "/var/www/gkr_myid/public/gudang2"
LOG_FILE = os.path.join(TARGET_DIR, "sync_gudang2.log")
# -----------------------------------------------

# Setup Logging
logging.basicConfig(
    level=logging.INFO,
    format='[%(asctime)s] %(message)s',
    datefmt='%Y-%m-%d %H:%M:%S',
    handlers=[
        # logging.FileHandler(LOG_FILE, encoding='utf-8'),
        logging.StreamHandler(sys.stdout)
    ]
)

def get_latest_file_url():
    """Mengambil daftar file dari web server Windows dan mencari file .xlsx terbaru."""
    try:
        # Request ke directory utama server Windows
        req = Request(WINDOWS_SERVER_URL, headers={'User-Agent': 'Mozilla/5.0'})
        response = urlopen(req, timeout=10)
        html = response.read().decode('utf-8', errors='ignore')
        
        # Mencari semua link (href) yang berakhiran .xlsx
        links = re.findall(r'href="([^"]+\.xlsx)"', html, re.IGNORECASE)
        
        if not links:
            return None
            
        max_month = -1
        latest_file_link = None
        
        for link in links:
            decoded_link = unquote(link)
            
            # Abaikan file temporary (misal: ~$09 STOCK.xlsx)
            if decoded_link.startswith('~$'):
                continue
                
            # Mencari angka bulan di awal nama file (contoh: "09" dari "09 STOCK...")
            match = re.search(r'^(\d{1,2})', decoded_link)
            if match:
                month = int(match.group(1))
                if month > max_month:
                    max_month = month
                    latest_file_link = link
                    
        if latest_file_link:
            if latest_file_link.startswith('http'):
                return latest_file_link
            else:
                return WINDOWS_SERVER_URL.rstrip('/') + '/' + latest_file_link.lstrip('/')
                
    except Exception as e:
        logging.error(f"[-] Gagal mengakses server Windows {WINDOWS_SERVER_URL}: {e}")
        return None
        
    return None

def download_file(url, dest_path):
    """Mengunduh file dari URL secara aman."""
    try:
        req = Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        response = urlopen(req, timeout=30)
        
        with open(dest_path, 'wb') as f:
            f.write(response.read())
            
        logging.info(f"[SUCCESS] Berhasil mengunduh data terbaru ke: {dest_path}")
        
    except Exception as e:
        logging.error(f"[-] Gagal mengunduh file dari {url}: {e}")
        sys.exit(1)

def main():
    logging.info("=" * 50)
    logging.info("Memulai sinkronisasi dari Server Windows (10.147.17.20)...")
    
    if not os.path.exists(TARGET_DIR):
        try:
            os.makedirs(TARGET_DIR)
            logging.info(f"[+] Membuat direktori target: {TARGET_DIR}")
        except Exception as e:
            logging.error(f"[-] Error membuat direktori {TARGET_DIR}: {e}")
            sys.exit(1)
            
    latest_url = get_latest_file_url()
    
    if latest_url:
        filename = unquote(latest_url.split('/')[-1])
        logging.info(f"[+] Terdeteksi file terbaru di server Windows: {filename}")
        
        dest_path = os.path.join(TARGET_DIR, filename)
        download_file(latest_url, dest_path)
    else:
        logging.error("[-] Tidak dapat mendeteksi file Excel terbaru.")
        logging.error("    (Pastikan Directory Listing aktif di server Windows 10.147.17.20)")
        sys.exit(1)
        
    logging.info("Sinkronisasi Selesai.")
    logging.info("=" * 50 + "\n")

if __name__ == "__main__":
    main()
