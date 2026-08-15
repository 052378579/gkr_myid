import requests
import pymysql
import sys
import time
import re

# --- KONFIGURASI ---
ERP_URL = "http://103.39.49.86:82"
ERP_USER = "budi@wickerkane.com"
ERP_PASS = "asd123"

db_config = {
    "host": "localhost",
    "user": "root",
    "password": "102013",
    "database": "gkr_myid",
    "autocommit": True
}
LIMIT = 5000

def get_session():
    session = requests.Session()
    login_url = f"{ERP_URL}/api/method/login"
    try:
        res = session.post(login_url, data={'usr': ERP_USER, 'pwd': ERP_PASS}, timeout=10)
        if res.status_code == 200:
            print("[OK] Berhasil login ke ERPNext via kredensial", flush=True)
            return session
        else:
            print("[ERROR] Login gagal. Periksa user/pass.", flush=True)
            sys.exit(1)
    except Exception as e:
        print(f"[ERROR] Tidak dapat terhubung ke ERP: {e}", flush=True)
        sys.exit(1)

def main():
    # Ambil argumen prefix dari PHP (misal: "FG-1" atau "FG-")
    prefix_bom = "FG-"
    if len(sys.argv) > 1:
        prefix_bom = sys.argv[1].strip()

    print(f"[INIT] Memulai Crawler ERPNext Inkremental (via Master Item) untuk Prefix: {prefix_bom}% ...", flush=True)
    time.sleep(1)

    try:
        conn = pymysql.connect(**db_config)
        cursor = conn.cursor()
        print("[OK] Terhubung ke Database MySQL lokal.", flush=True)
    except Exception as e:
        print(f"[ERROR DB] {str(e)}", flush=True)
        sys.exit(1)

    session = get_session()
    
    offset = 0
    total_tersimpan = 0
    
    while True:
        try:
            print(f"Menarik baris {offset} sampai {offset + LIMIT} dari server...", flush=True)
            # URL API sekarang MENGANDALKAN filter ERPNext!
            # Fields: name (Kode Item), item_name (Nama pendek), description (Spek panjang)
            api_endpoint = f"{ERP_URL}/api/resource/Item?limit_page_length={LIMIT}&limit_start={offset}&fields=[\"name\",\"item_name\",\"description\"]&filters=[[\"name\",\"like\",\"{prefix_bom}%\"]]"
            response = session.get(api_endpoint, timeout=30)
            
            if response.status_code != 200:
                print(f"[ERROR API] Response {response.status_code}: {response.text}", flush=True)
                break
                
            data = response.json()
            items = data.get("data", [])
            
            if not items:
                break
                
            batch_data = []
            sampah = 0
            # Double check filtering lokal
            for item in items:
                raw_name = item.get("name", "")
                item_name = item.get("item_name", "") or ""
                description = item.get("description", "") or ""
                
                # Gabungkan nama pendek dan spek menjadi satu teks mentah panjang
                # Agar algoritma pemisah kolom (regex) tetap bekerja
                item_master = f"{item_name} {description}".strip()
                
                # Hanya simpan yang punya kata "FG-"
                match = re.search(r'(FG-\d+)', raw_name.upper())
                if match:
                    kode_bom_bersih = match.group(1)

                    # 1. Aturan Wajib (Hanya izinkan awalan 1, 2, 3, 4)
                    if not (kode_bom_bersih.startswith('FG-1') or 
                            kode_bom_bersih.startswith('FG-2') or 
                            kode_bom_bersih.startswith('FG-3') or 
                            kode_bom_bersih.startswith('FG-4')):
                        sampah += 1
                        continue
                        
                    # 2. Pengecualian Spesifik (Blokir mutlak)
                    if kode_bom_bersih in ['FG-1', 'FG-10000']:
                        sampah += 1
                        continue

                    batch_data.append((kode_bom_bersih, item_master))
                else:
                    sampah += 1

            if batch_data:
                sql = """
                    INSERT IGNORE INTO gkr_erp (kode_bom, item_master, terakhir_ditarik) 
                    VALUES (%s, %s, CURRENT_TIMESTAMP)
                """
                cursor.executemany(sql, batch_data)
                total_tersimpan += len(batch_data)
                
            print(f"-> Diabaikan: {sampah} baris sampah.", flush=True)
            print(f"-> Tersimpan & Ter-Update: {len(batch_data)} baris FG murni (Total: {total_tersimpan})", flush=True)
                
            offset += LIMIT
            time.sleep(1) 
            
        except requests.exceptions.RequestException as e:
            print(f"[ERROR JARINGAN] Terputus saat narik data: {str(e)}", flush=True)
            break
        except Exception as e:
            print(f"[ERROR SYSTEM] {str(e)}", flush=True)
            break
            
    print(f"[SELESAI] Penarikan selesai! Total {total_tersimpan} data Finished Goods (FG) tersimpan.", flush=True)
    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
