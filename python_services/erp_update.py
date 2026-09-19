import requests
import pymysql
import sys
import time
import json
import random

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
LIMIT = 100

def get_session():
    session = requests.Session()
    login_url = f"{ERP_URL}/api/method/login"
    try:
        res = session.post(login_url, data={'usr': ERP_USER, 'pwd': ERP_PASS}, timeout=15)
        if res.status_code == 200:
            return session
        else:
            print("[ERROR] Login gagal. Periksa user/pass.", flush=True)
            sys.exit(1)
    except Exception as e:
        print(f"[ERROR] Tidak dapat terhubung ke ERP: {e}", flush=True)
        sys.exit(1)

def fmt_angka(v):
    if v is None: return ""
    try:
        f = float(v)
        return str(int(f)) if f == int(f) else str(f)
    except: return ""

def rakit_dimensi(p, l, t):
    parts = [fmt_angka(v) for v in [p, l, t] if v is not None and fmt_angka(v) != "0" and fmt_angka(v) != "0.0" and fmt_angka(v) != ""]
    return "X".join(parts) if parts else ""

def main():
    print("[INIT] Memulai Sinkronisasi Inkremental...", flush=True)
    time.sleep(1)

    try:
        conn = pymysql.connect(**db_config)
        cursor = conn.cursor()
        print("[OK] Terhubung ke Database MySQL lokal.", flush=True)
    except Exception as e:
        print(f"[ERROR DB] {str(e)}", flush=True)
        sys.exit(1)

    # Dapatkan MAX erp_modified
    cursor.execute("SELECT MAX(erp_modified) FROM gkr_erp")
    row = cursor.fetchone()
    max_modified = row[0] if row else None

    if not max_modified:
        print("[SELESAI] Belum ada data di database lokal. Harap gunakan tombol HARD RESET & CRAWL terlebih dahulu.", flush=True)
        sys.exit(0)

    # Format datetime to string for Frappe filter (YYYY-MM-DD HH:MM:SS)
    max_modified_str = max_modified.strftime('%Y-%m-%d %H:%M:%S')
    print(f"-> Mencari perubahan setelah: {max_modified_str}", flush=True)

    session = get_session()
    
    # Tarik BOM yang dimodifikasi
    filters = json.dumps([["modified", ">", max_modified_str], ["item","like","FG-%"], ["is_default","=",1], ["is_active","=",1]])
    fields = json.dumps(["name","item","item_name","packing","finishing","buyer","modified"])
    api_endpoint = f"{ERP_URL}/api/resource/BOM?limit_page_length={LIMIT}&fields={fields}&filters={filters}"
    
    # Retry mechanism untuk initial fetch
    for attempt in range(5):
        try:
            response = session.get(api_endpoint, timeout=45)
            if response.status_code == 200:
                items = response.json().get("data", [])
                break
            elif response.status_code in [401, 403]:
                time.sleep(5)
                session = get_session()
                continue
            else:
                wait_t = 5 * (attempt + 1)
                time.sleep(wait_t)
        except Exception as e:
            if attempt < 4:
                time.sleep(3 ** attempt + random.uniform(1,3))
            else:
                print(f"[ERROR API] Gagal menarik daftar modifikasi: {e}", flush=True)
                sys.exit(1)

    if not items:
        print("[SELESAI] Data Anda sudah paling mutakhir. Tidak ada perubahan baru di ERP.", flush=True)
        sys.exit(0)

    print(f"-> Ditemukan {len(items)} perubahan BOM baru. Memproses detail...", flush=True)

    processed = 0
    sampah = 0
    
    for item in items:
        bom_name = item.get("name", "")
        kode_bom = item.get("item", "")
        item_name = item.get("item_name", "")
        packing = item.get("packing", "")
        finishing = item.get("finishing", "")
        buyer = item.get("buyer", "")
        modified = item.get("modified", None)
        
        kode_upper = kode_bom.upper()
        
        if kode_upper in ['FG-1', 'FG-10000'] or len(kode_upper) != 8 or not kode_upper[3:].isdigit():
            sampah += 1
            continue

        # Tarik detail untuk dimensi dan harga
        dimensi = ""
        load_40 = ""
        load_40_hc = ""
        min_price = None
        sug_price = None

        detail_endpoint = f"{ERP_URL}/api/resource/BOM/{bom_name}"
        
        # Mekanisme Retry tangguh seperti erp_ekstrak.py
        for attempt in range(5):
            try:
                det_res = session.get(detail_endpoint, timeout=30)
                if det_res.status_code == 200:
                    det_data = det_res.json().get("data", {})
                    dimensi = rakit_dimensi(det_data.get("panjang_barang_jadi"), det_data.get("lebar_barang_jadi"), det_data.get("tinggi_barang_jadi"))
                    min_price = det_data.get("minimum_selling_price")
                    sug_price = det_data.get("suggested_selling_price")
                    
                    for lt in det_data.get("loadibility_table", []):
                        komp = lt.get("komponen_container")
                        pcs = lt.get("pcs")
                        if komp == "40 Inch" and pcs is not None: load_40 = fmt_angka(pcs)
                        elif komp == "40 Inch HC" and pcs is not None: load_40_hc = fmt_angka(pcs)
                    
                    time.sleep(random.uniform(0.1, 0.4)) # Throttling organic
                    break
                elif det_res.status_code in [401, 403]:
                    print(f"[WARN] Sesi ditolak ({det_res.status_code}). Re-login...", flush=True)
                    time.sleep(5)
                    session = get_session()
                    continue
                elif det_res.status_code == 429:
                    wait_time = 10 * (attempt + 1)
                    print(f"[WARN] 429 Terlalu banyak request. Tunggu {wait_time}s...", flush=True)
                    time.sleep(wait_time)
                    continue
                elif det_res.status_code in [500, 502, 503, 504]:
                    wait_time = 5 * (attempt + 1)
                    print(f"[WARN] Server error {det_res.status_code}. Tunggu {wait_time}s...", flush=True)
                    time.sleep(wait_time)
                    continue
                else:
                    break # Kesalahan permanen seperti 404
            except requests.exceptions.RequestException as e:
                wait_time = (3 ** attempt) + random.uniform(1, 3)
                if attempt < 4:
                    print(f"[WARN] Terputus pada {bom_name} (Mencoba ulang {attempt+1}/5 - tunggu {wait_time:.1f}s)...", flush=True)
                    time.sleep(wait_time)
                else:
                    print(f"[ERROR JARINGAN] Gagal pada {bom_name}: {str(e)}", flush=True)
            except Exception as e:
                break # Lanjut dengan data kosong jika error tak terduga

        if isinstance(packing, str) and len(packing) > 100: packing = packing[:97] + "..."
        if isinstance(finishing, str) and len(finishing) > 100: finishing = finishing[:97] + "..."
        if isinstance(buyer, str) and len(buyer) > 100: buyer = buyer[:97] + "..."

        # Update MySQL
        sql_update = """
            INSERT INTO gkr_erp (kode_bom, bom_name, item_name, packing, finishing, buyer, erp_modified, dimensi, load_40, load_40_hc, minimum_selling_price, suggested_selling_price) 
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
            ON DUPLICATE KEY UPDATE
            bom_name = VALUES(bom_name),
            item_name = VALUES(item_name),
            packing = VALUES(packing),
            finishing = VALUES(finishing),
            buyer = VALUES(buyer),
            erp_modified = VALUES(erp_modified),
            dimensi = VALUES(dimensi),
            load_40 = VALUES(load_40),
            load_40_hc = VALUES(load_40_hc),
            minimum_selling_price = VALUES(minimum_selling_price),
            suggested_selling_price = VALUES(suggested_selling_price)
        """
        cursor.execute(sql_update, (kode_bom, bom_name, item_name, packing, finishing, buyer, modified, dimensi, load_40, load_40_hc, min_price, sug_price))
        processed += 1
        print(f"[UPDATE] {kode_bom} berhasil diperbarui (termasuk Dimensi & Harga).", flush=True)

    print(f"[SELESAI] Sinkronisasi Inkremental selesai. Berhasil memperbarui {processed} baris.", flush=True)
    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()

