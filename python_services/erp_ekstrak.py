import requests
import pymysql
import sys
import time
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

def is_valid_bom_name(bom_name):
    if not bom_name.upper().startswith("BOM-FG-"):
        return False
    sisa = bom_name[7:]
    parts = sisa.split("-")
    if len(parts) < 2:
        return False
    return len(parts[0]) == 5 and parts[0].isdigit()

def fmt_angka(v):
    if v is None:
        return ""
    try:
        f = float(v)
        return str(int(f)) if f == int(f) else str(f)
    except:
        return ""

def rakit_dimensi(p, l, t):
    parts = [fmt_angka(v) for v in [p, l, t] if v is not None and fmt_angka(v) != "0" and fmt_angka(v) != "0.0" and fmt_angka(v) != ""]
    return "X".join(parts) if parts else ""

def main():
    is_incremental = "--inc" in sys.argv
    print(f"[INIT] Memulai Ekstraksi Detail BOM (Mode: {'LANJUTAN' if is_incremental else 'FULL'}) ...", flush=True)
    time.sleep(1)

    try:
        conn = pymysql.connect(**db_config)
        cursor = conn.cursor()
        print("[OK] Terhubung ke Database MySQL lokal.", flush=True)
    except Exception as e:
        print(f"[ERROR DB] {str(e)}", flush=True)
        sys.exit(1)

    # Ambil list bom_name
    if is_incremental:
        sql_select = "SELECT kode_bom, bom_name FROM gkr_erp WHERE dimensi IS NULL AND bom_name IS NOT NULL"
    else:
        sql_select = "SELECT kode_bom, bom_name FROM gkr_erp WHERE bom_name IS NOT NULL"

    cursor.execute(sql_select)
    rows = cursor.fetchall()

    if not rows:
        print("[SELESAI] Tidak ada data yang perlu diekstrak.", flush=True)
        cursor.close()
        conn.close()
        return

    total_rows = len(rows)
    print(f"-> Ditemukan {total_rows} baris untuk diekstrak. Memproses...", flush=True)
    
    session = get_session()
    
    processed = 0
    sampah = 0
    error_count = 0

    for kode_bom, bom_name in rows:
        if not is_valid_bom_name(bom_name):
            sampah += 1
            continue
            
        max_retries = 5
        api_endpoint = f"{ERP_URL}/api/resource/BOM/{bom_name}"

        for attempt in range(max_retries):
            try:
                response = session.get(api_endpoint, timeout=30)
                
                if response.status_code == 200:
                    data = response.json().get("data", {})
                    
                    # Ekstrak Dimensi
                    p = data.get("panjang_barang_jadi")
                    l = data.get("lebar_barang_jadi")
                    t = data.get("tinggi_barang_jadi")
                    dimensi = rakit_dimensi(p, l, t)
                    
                    # Ekstrak Harga
                    min_price = data.get("minimum_selling_price")
                    sug_price = data.get("suggested_selling_price")
                    
                    # Ekstrak Loadibility
                    load_40 = ""
                    load_40_hc = ""
                    loadibility = data.get("loadibility_table", [])
                    for lt in loadibility:
                        komp = lt.get("komponen_container")
                        pcs = lt.get("pcs")
                        if komp == "40 Inch" and pcs is not None:
                            load_40 = fmt_angka(pcs)
                        elif komp == "40 Inch HC" and pcs is not None:
                            load_40_hc = fmt_angka(pcs)
                    
                    # Update ke MySQL
                    sql_update = """
                        UPDATE gkr_erp 
                        SET dimensi = %s, 
                            load_40 = %s, 
                            load_40_hc = %s, 
                            minimum_selling_price = %s, 
                            suggested_selling_price = %s
                        WHERE kode_bom = %s
                    """
                    cursor.execute(sql_update, (dimensi, load_40, load_40_hc, min_price, sug_price, kode_bom))
                    processed += 1
                    
                    # Cetak progress setiap 100 baris
                    if processed % 100 == 0:
                        print(f"[EKSTRAK] {processed} / {total_rows} baris selesai... (terakhir: {bom_name})", flush=True)

                    # Throttling Anti-Ban (0.1s - 0.4s)
                    time.sleep(random.uniform(0.1, 0.4))
                    break
                    
                elif response.status_code in [401, 403]:
                    print(f"[WARN] Sesi ditolak ({response.status_code}). Re-login...", flush=True)
                    time.sleep(5)
                    session = get_session()
                    continue
                elif response.status_code == 429:
                    wait_time = 10 * (attempt + 1)
                    print(f"[WARN] 429 Terlalu banyak request. Tunggu {wait_time}s...", flush=True)
                    time.sleep(wait_time)
                    continue
                elif response.status_code in [500, 502, 503, 504]:
                    wait_time = 5 * (attempt + 1)
                    print(f"[WARN] Server error {response.status_code}. Tunggu {wait_time}s...", flush=True)
                    time.sleep(wait_time)
                    continue
                else:
                    error_count += 1
                    if error_count % 10 == 0:
                        print(f"[WARN] Gagal akses API untuk {bom_name}, status: {response.status_code}", flush=True)
                    break # Error yang tidak dapat dipulihkan (misal 404)
                    
            except requests.exceptions.RequestException as e:
                wait_time = (3 ** attempt) + random.uniform(1, 3)
                if attempt < max_retries - 1:
                    print(f"[WARN] Terputus pada {bom_name} (Mencoba ulang {attempt+1}/{max_retries} - tunggu {wait_time:.1f}s)...", flush=True)
                    time.sleep(wait_time)
                else:
                    print(f"[ERROR JARINGAN] Gagal permanen pada {bom_name}: {str(e)}", flush=True)
                    break
            except Exception as e:
                print(f"[ERROR SYSTEM] Pada {bom_name}: {str(e)}", flush=True)
                break
                
    print(f"[SELESAI] Ekstraksi selesai! Berhasil ekstrak: {processed} baris. Ditolak: {sampah}. Error API: {error_count}.", flush=True)
    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()

