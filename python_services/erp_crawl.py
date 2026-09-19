import requests
import pymysql
import sys
import time
import json

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
LIMIT = 1000

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
    target_prefixes = ["FG-1", "FG-2", "FG-3", "FG-4"]

    try:
        conn = pymysql.connect(**db_config)
        cursor = conn.cursor()
        print("[OK] Terhubung ke Database MySQL lokal.", flush=True)
    except Exception as e:
        print(f"[ERROR DB] {str(e)}", flush=True)
        sys.exit(1)

    session = get_session()
    total_tersimpan_global = 0
    
    for p_bom in target_prefixes:
        print(f"[INIT] Memulai Crawling Masif (Direct API) untuk Prefix: {p_bom}% ...", flush=True)
        time.sleep(1)
        offset = 0
        
        while True:
            try:
                print(f"Menarik baris {offset} sampai {offset + LIMIT} (Prefix {p_bom}%)...", flush=True)
                
                # Tambahkan 'modified' ke daftar fields
                filters = json.dumps([["item","like",f"{p_bom}%"], ["is_default","=",1], ["is_active","=",1]])
                fields = json.dumps(["name","item","item_name","packing","finishing","buyer","modified"])
                api_endpoint = f"{ERP_URL}/api/resource/BOM?limit_page_length={LIMIT}&limit_start={offset}&fields={fields}&filters={filters}"
                
                response = session.get(api_endpoint, timeout=30)
                
                if response.status_code != 200:
                    print(f"[ERROR API] Response {response.status_code}: {response.text[:200]}", flush=True)
                    break
                    
                data = response.json()
                items = data.get("data", [])
                
                if not items:
                    break
                    
                batch_data = []
                sampah = 0
                
                for item in items:
                    bom_name = item.get("name", "")
                    kode_bom = item.get("item", "")
                    item_name = item.get("item_name", "")
                    packing = item.get("packing", "")
                    finishing = item.get("finishing", "")
                    buyer = item.get("buyer", "")
                    modified = item.get("modified", None)
                    
                    if isinstance(packing, str) and len(packing) > 100: packing = packing[:97] + "..."
                    if isinstance(finishing, str) and len(finishing) > 100: finishing = finishing[:97] + "..."
                    if isinstance(buyer, str) and len(buyer) > 100: buyer = buyer[:97] + "..."
                    
                    kode_upper = kode_bom.upper()
                    
                    if kode_upper in ['FG-1', 'FG-10000']:
                        sampah += 1
                        continue
                        
                    if len(kode_upper) != 8:
                        sampah += 1
                        continue
                        
                    if not kode_upper[3:].isdigit():
                        sampah += 1
                        continue

                    batch_data.append((kode_bom, bom_name, item_name, packing, finishing, buyer, modified))

                if batch_data:
                    sql = """
                        INSERT INTO gkr_erp (kode_bom, bom_name, item_name, packing, finishing, buyer, erp_modified) 
                        VALUES (%s, %s, %s, %s, %s, %s, %s)
                        ON DUPLICATE KEY UPDATE
                        bom_name = VALUES(bom_name),
                        item_name = VALUES(item_name),
                        packing = VALUES(packing),
                        finishing = VALUES(finishing),
                        buyer = VALUES(buyer),
                        erp_modified = VALUES(erp_modified)
                    """
                    cursor.executemany(sql, batch_data)
                    total_tersimpan_global += len(batch_data)
                    
                print(f"-> Diabaikan (Varian/Kotoran): {sampah} baris.", flush=True)
                print(f"-> Tersimpan & Ter-Update: {len(batch_data)} baris (Total: {total_tersimpan_global})", flush=True)
                    
                offset += LIMIT
                time.sleep(1) 
                
            except requests.exceptions.RequestException as e:
                print(f"[ERROR JARINGAN] Terputus saat narik data: {str(e)}", flush=True)
                break
            except Exception as e:
                print(f"[ERROR SYSTEM] {str(e)}", flush=True)
                break
                
    print(f"[SELESAI] Penarikan selesai! Keseluruhan {total_tersimpan_global} BOM ditarik lengkap dengan Timestamp.", flush=True)
    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
