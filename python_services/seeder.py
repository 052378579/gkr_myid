import os
import mysql.connector
from PIL import Image
import imagehash

# Konfigurasi Database
DB_HOST = "localhost"
DB_USER = "root"
DB_PASS = "102013"
DB_NAME = "gkr_myid"

# Folder gambar untuk seeding
SEEDING_DIR = "/var/www/gkr_myid/FOTO/SEEDING/"
# Prefix yang biasanya tersimpan di database cari_images.imageUrl (jika ada struktur folder tertentu yang hilang)
# Berdasarkan query sebelumnya, imageUrl formatnya "BUYER/.../nama.jpg"
# Tapi jika FOTO/SEEDING/ adalah kumpulan raw images, kita mungkin mencocokkannya berdasarkan nama file.
# Kita asumsikan kita meng-update berdasarkan nama file saja (pakai LIKE) atau mencocokkan sebagian path.

def connect_db():
    return mysql.connector.connect(
        host=DB_HOST,
        user=DB_USER,
        password=DB_PASS,
        database=DB_NAME
    )

def main():
    if not os.path.exists(SEEDING_DIR):
        print(f"Directory {SEEDING_DIR} does not exist.")
        return

    db = connect_db()
    cursor = db.cursor()

    processed = 0
    updated = 0

    print("Memulai proses seeding hash gambar...")

    for root, dirs, files in os.walk(SEEDING_DIR):
        for file in files:
            if file.lower().endswith(('.png', '.jpg', '.jpeg', '.webp')):
                file_path = os.path.join(root, file)
                
                try:
                    # Buka dan hash
                    img = Image.open(file_path)
                    hash_val = str(imagehash.phash(img))
                    
                    # Update database (cari yang nama filenya sama)
                    # Karena struktur folder mungkin berbeda, kita pakai LIKE '%nama_file'
                    # Pastikan escape karakter jika ada quote di nama file
                    query = "UPDATE cari_images SET image_hash = %s WHERE imageUrl LIKE %s"
                    search_pattern = f"%{file}"
                    
                    cursor.execute(query, (hash_val, search_pattern))
                    db.commit()
                    
                    if cursor.rowcount > 0:
                        print(f"[OK] Diperbarui: {file} -> {hash_val} ({cursor.rowcount} baris)")
                        updated += 1
                    else:
                        print(f"[SKIP] Tidak ditemukan di DB: {file} -> {hash_val}")
                        
                    processed += 1
                    
                except Exception as e:
                    print(f"[ERROR] Gagal memproses {file}: {str(e)}")

    print("-" * 30)
    print(f"Total diproses : {processed}")
    print(f"Total diupdate : {updated}")
    
    cursor.close()
    db.close()

if __name__ == "__main__":
    main()
