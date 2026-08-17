import pymysql
import sys
import time
import re

# --- KONFIGURASI DATABASE ---
db_config = {
    "host": "localhost",
    "user": "root",
    "password": "102013",
    "database": "gkr_myid",
    "autocommit": True
}

# --- KAMUS EKSTRAKSI ---
KAMUS_WEAVING = ["POLYPEEL", "VIRO", "REHAU", "PE RATTAN", "ROPE", "POLYROD", "POLYROOD", "POLYSTRAP", "WEBBING OPEN", "SANDED PEEL"]
KAMUS_FABRIC = ["AGORA", "SUNBRELLA", "CANVAS", "UPHOLSTERY", "APHRODITE"]

def hapus_duplikasi(teks, min_len=30):
    n = len(teks)
    # Cari dari substring yang paling panjang ke yang paling pendek (min_len)
    for l in range(n // 2, min_len - 1, -1):
        for i in range(n - 2 * l + 1):
            sub = teks[i:i+l]
            idx2 = teks.find(sub, i + 1)
            if idx2 != -1:
                # Ditemukan duplikasi, hapus kemunculan kedua
                return teks[:idx2] + teks[idx2+l:]
    return teks

def ekstrak_kolom(item_master):
    if not item_master:
        return None, None, None, None, None
        
    # 1. Hapus semua tag HTML (misal <div>, <br>) yang mungkin terbawa dari ERPNext
    teks_bersih = re.sub(r'<[^>]+>', ' ', item_master)
    
    # 2. Hapus entitas HTML seperti &nbsp;
    teks_bersih = re.sub(r'&[a-zA-Z0-9#]+;', ' ', teks_bersih)
    
    # 3. Hapus semua kemunculan FG-<angka> agar tidak ikut terekstrak
    teks = re.sub(r'\bFG-\d+\b', '', teks_bersih).upper()
    
    # 4. Rapikan spasi berlebih akibat penghapusan tag
    teks = re.sub(r'\s+', ' ', teks).strip()
    
    # 5. Eksekusi Pemburu Duplikasi (Hapus teks kembar minimal 30 karakter)
    teks = hapus_duplikasi(teks)
    
    dimensi = None
    item_name = None
    material = None
    weaving = None
    fabric = None
    
    # 1. Ekstrak Dimensi (Pemisah spasi & pertahankan kurung, dukung akhiran CM dengan spasi)
    regex_dim = r'\s([A-Za-z\.]*\d[\d\(\)\.,]*[xX*][\d\(\)\.,xX*/]+(?:\s*(?:CM|cm|INCH|inch|INCHES|inches))?(?:\s*\([\d\.,xX*/\s]+(?:CM|cm|INCH|inch|INCHES|inches)?\))?)(?=\s|$|,)'
    match_dim = re.search(regex_dim, teks)
    
    if match_dim:
        dimensi = match_dim.group(1).strip()
        item_name = teks[:match_dim.start()].strip()
        material = teks[match_dim.end():].strip()
        
        # Post-Extraction Cleansing: Musnahkan sisa-sisa dimensi dari item_name dan material
        item_name = item_name.replace(dimensi, "").strip()
        material = material.replace(dimensi, "").strip()
        
        # Bersihkan spasi ganda yang mungkin terjadi akibat penghapusan di atas
        item_name = re.sub(r'\s+', ' ', item_name).strip()
        material = re.sub(r'\s+', ' ', material).strip()
        
        if material == "":
            material = None
    else:
        item_name = teks
        material = None
        
    # 2. Ekstrak Weaving & Fabric dari Material (atau teks jika dimensi tidak ada)
    sumber_ekstraksi = material if material else teks
    
    if sumber_ekstraksi:
        # Weaving
        weaving_list = [k for k in KAMUS_WEAVING if k in sumber_ekstraksi]
        weaving = ", ".join(weaving_list) if weaving_list else None
        
        # Fabric (beserta semua teks hingga akhir, termasuk awalan QDF/IDF jika ada)
        regex_fab = r'((?:(?:QDF|IDF)[\s\-]*)?(?:' + '|'.join(KAMUS_FABRIC) + r')\b.*)'
        match_fab = re.search(regex_fab, sumber_ekstraksi)
        
        if match_fab:
            fabric = match_fab.group(1).strip()
            # Hapus fabric dari material
            material = sumber_ekstraksi[:match_fab.start()].strip()
            # Hapus karakter pemisah seperti '-' atau '+' atau spasi yang tertinggal di akhir material
            material = re.sub(r'[\-\+\s]+$', '', material).strip()
            if material == "":
                material = None
    
    return item_name, dimensi, material, weaving, fabric

def main():
    print("Memulai Mesin Ekstraksi (Pemisah Kolom) Python...", flush=True)
    time.sleep(1) 
    
    try:
        conn = pymysql.connect(**db_config)
        cursor = conn.cursor()
        print("[OK] Terhubung ke Database MySQL.", flush=True)
        
        # FIX: Perbesar kapasitas kolom untuk mencegah error 1406 Data too long
        cursor.execute("ALTER TABLE gkr_erp MODIFY item_name TEXT, MODIFY material TEXT, MODIFY weaving TEXT, MODIFY fabric TEXT, MODIFY dimensi VARCHAR(255)")
        print("[OK] Struktur tabel gkr_erp otomatis di-upgrade ke kapasitas TEXT.", flush=True)
        
    except Exception as e:
        print(f"[ERROR DB] {str(e)}", flush=True)
        sys.exit(1)

    print("[INIT] Mereset seluruh kolom hasil ekstraksi...", flush=True)
    try:
        sql_reset = """
            UPDATE gkr_erp 
            SET item_name = NULL, 
                dimensi = NULL, 
                material = NULL, 
                weaving = NULL, 
                fabric = NULL
        """
        cursor.execute(sql_reset)
        print("[OK] Seluruh kolom (kecuali kode_bom & item_master) berhasil dikosongkan.", flush=True)
    except Exception as e:
        print(f"[ERROR RESET] {str(e)}", flush=True)

    print("[INIT] Mengambil seluruh data untuk diekstrak ulang...", flush=True)
    
    # Ambil SEMUA data karena baru saja direset
    sql_select = "SELECT kode_bom, item_master FROM gkr_erp"
    cursor.execute(sql_select)
    rows = cursor.fetchall()
    
    total_rows = len(rows)
    if total_rows == 0:
        print("[SELESAI] Tidak ada data baru yang perlu diekstrak.", flush=True)
        cursor.close()
        conn.close()
        return

    print(f"-> Ditemukan {total_rows} baris untuk diekstrak. Memproses...", flush=True)
    
    update_data = []
    for row in rows:
        kode_bom = row[0]
        item_master = row[1]
        
        item_name, dimensi, material, weaving, fabric = ekstrak_kolom(item_master)
        update_data.append((item_name, dimensi, material, weaving, fabric, kode_bom))

    # Eksekusi Update
    try:
        sql_update = """
            UPDATE gkr_erp 
            SET item_name = %s, 
                dimensi = %s, 
                material = %s, 
                weaving = %s, 
                fabric = %s,
                terakhir_diekstrak = CURRENT_TIMESTAMP
            WHERE kode_bom = %s
        """
        batch_size = 1000
        processed = 0
        for i in range(0, len(update_data), batch_size):
            batch = update_data[i:i+batch_size]
            cursor.executemany(sql_update, batch)
            processed += len(batch)
            print(f"-> Ter-ekstrak: {processed} / {total_rows} baris...", flush=True)
            
        print("[SELESAI] Seluruh proses pemisahan kolom berhasil!", flush=True)
    except Exception as e:
        print(f"[ERROR UPDATE] {str(e)}", flush=True)
        
    cursor.close()
    conn.close()

if __name__ == "__main__":
    main()
