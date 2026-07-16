import mysql.connector
import requests
from io import BytesIO
from PIL import Image
import imagehash
import urllib.parse
import time
import sys

# Konfigurasi Database
DB_HOST = "localhost"
DB_USER = "root"
DB_PASS = "102013"
DB_NAME = "gkr_myid"

# Base URL gambar (sesuaikan jika gambar ada di server lain)
BASE_URL = "http://foto.budi.biz.id/"

def main():
    try:
        db = mysql.connector.connect(
            host=DB_HOST,
            user=DB_USER,
            password=DB_PASS,
            database=DB_NAME
        )
        cursor = db.cursor(dictionary=True)
    except Exception as e:
        print(f"Gagal koneksi ke database: {e}")
        sys.exit(1)

    # Hanya ambil gambar yang hash-nya masih kosong dan link-nya tidak rusak
    cursor.execute("SELECT id, imageUrl FROM cari_images WHERE image_hash IS NULL AND broken = 0")
    rows = cursor.fetchall()
    
    if not rows:
        print("Tidak ada gambar yang perlu diproses (semua sudah memiliki hash).")
        cursor.close()
        db.close()
        return

    print(f"Memulai proses crawler untuk {len(rows)} gambar...")
    processed = 0
    failed = 0

    # Gunakan session HTTP untuk mempercepat koneksi (Reuse TCP)
    session = requests.Session()
    # Identitas agen browser (menghindari diblokir server)
    session.headers.update({'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)'})

    for row in rows:
        img_id = row['id']
        img_url_path = row['imageUrl']
        
        # Hapus awalan '/' jika ada
        if img_url_path.startswith('/'):
            img_url_path = img_url_path[1:]
            
        # Encode URL untuk menangani spasi secara akurat (misal: QFH605 ST.jpg -> QFH605%20ST.jpg)
        # Kita split berdasarkan '/', lalu encode setiap folder/file, dan gabung kembali
        parts = img_url_path.split('/')
        encoded_parts = [urllib.parse.quote(p) for p in parts]
        encoded_path = '/'.join(encoded_parts)
        
        full_url = BASE_URL + encoded_path
        
        try:
            # Unduh gambar (maksimal tunggu 10 detik per gambar)
            response = session.get(full_url, timeout=10)
            
            if response.status_code == 200:
                # Muat dari RAM (ByteStream) tanpa menyimpannya ke Harddisk
                img = Image.open(BytesIO(response.content))
                
                # Kalkulasi pHash (Perceptual Hash) 64-bit
                hash_val = str(imagehash.phash(img))
                
                # Update ke Database
                update_cursor = db.cursor()
                update_cursor.execute("UPDATE cari_images SET image_hash = %s WHERE id = %s", (hash_val, img_id))
                db.commit()
                update_cursor.close()
                
                print(f"[OK] ID {img_id}: {hash_val} -> {full_url}")
                processed += 1
            else:
                print(f"[FAIL] ID {img_id}: HTTP {response.status_code} -> {full_url}")
                failed += 1
                
        except requests.exceptions.RequestException as e:
            print(f"[NETWORK ERROR] ID {img_id}: -> {full_url}")
            failed += 1
        except Exception as e:
            print(f"[PROCESS ERROR] ID {img_id}: {str(e)} -> {full_url}")
            failed += 1
            
        # Beri jeda 0.1 detik untuk mencegah serangan DDoS (Rate Limiting) pada server tujuan
        time.sleep(0.1)

    print("-" * 40)
    print(f"PROSES SELESAI!")
    print(f"Berhasil di-hash : {processed}")
    print(f"Gagal di-hash    : {failed}")
    
    cursor.close()
    db.close()

if __name__ == "__main__":
    main()
