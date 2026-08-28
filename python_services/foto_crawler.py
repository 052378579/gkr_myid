import os
import sys
import pymysql
import json
import re
db_config = {}
try:
    with open('/var/www/gkr_myid/.env', 'r') as f:
        for line in f:
            line = line.strip()
            if line.startswith('database.default.'):
                key, val = line.split('=', 1)
                db_config[key.strip()] = val.strip().strip("'").strip('"')
except:
    pass

DB_HOST = db_config.get('database.default.hostname', 'localhost')
DB_USER = db_config.get('database.default.username', 'root')
DB_PASS = db_config.get('database.default.password', '')
DB_NAME = db_config.get('database.default.database', 'gkr_myid')
DB_PORT = int(db_config.get('database.default.port', 3306))

def out(msg):
    print(msg, flush=True)

try:
    conn = pymysql.connect(host=DB_HOST, user=DB_USER, password=DB_PASS, database=DB_NAME, port=DB_PORT, autocommit=True)
    cursor = conn.cursor(pymysql.cursors.DictCursor)
except Exception as e:
    out(f"<span style='color: #dc3545;'>[ERROR] Gagal koneksi database: {e}</span><br>")
    sys.exit(1)

target_dir = sys.argv[1] if len(sys.argv) > 1 else "/var/www/FOTO"
target_dir = target_dir.replace('\\', '/').rstrip('/')
root_path = "/var/www/FOTO"

if not target_dir.startswith(root_path):
    out(f"<span style='color: #dc3545;'>[ERROR] Path harus berawalan {root_path}</span><br>")
    sys.exit(1)

out(f"<span style='color: #a9a9a9;'>Memulai scan direktori lokal (1 Produk = 1 Baris):</span> {target_dir}<br>")

folders_to_scan = [root_path + '/BUYER', root_path + '/GRACIA', root_path + '/SWATCHES', root_path + '/WEB'] if target_dir == root_path else [target_dir]

items_added = 0

for folder in folders_to_scan:
    if 'SAMPLE GRACIA' in folder.upper():
        out(f"<span style='color: #ffc107;'>[SKIP]</span> <span style='color: #a9a9a9;'>Mengabaikan direktori terlarang: {folder}</span><br>")
        continue

    if not os.path.isdir(folder):
        out(f"<span style='color: #dc3545;'>[ERROR] Folder tidak ditemukan:</span> {folder}<br>")
        continue

    for root, dirs, files in os.walk(folder):
        dirs[:] = [d for d in dirs if 'SAMPLE GRACIA' not in d.upper()]
        
        for file in files:
            if 'SAMPLE GRACIA' in root.upper():
                continue
                
            ext = file.split('.')[-1].lower()
            if ext in ['jpg', 'jpeg', 'png', 'webp']:
                filename_without_ext = os.path.splitext(file)[0]
                parent_folder = os.path.basename(root)
                
                relative_path = os.path.relpath(os.path.join(root, file), root_path).replace('\\', '/')
                
                if file.upper().startswith('IMG_') or file.upper().startswith('DCIM_'):
                    title_base = parent_folder.replace('-', ' ').replace('_', ' ').title()
                    title = title_base
                    alt = title_base
                    description = title_base
                    keywords = ", ".join([w for w in title_base.lower().split() if w])
                    bom_code = None
                else:
                    clean_filename = re.sub(r'[ _-]*(depan|belakang|samping|perspektif|detail|_b|_c|_d|_e)$', '', filename_without_ext, flags=re.IGNORECASE)
                    
                    bom_code = None
                    base_text = clean_filename
                    
                    m = re.search(r'(?:\(|\s|_|-)*(fg[-_\s]*\d+|bom[-_\s]*[a-z0-9-]+)(?:\)|\s|_|-)*', clean_filename, re.IGNORECASE)
                    if m:
                        digits = re.sub(r'[^0-9]', '', m.group(1))
                        if digits:
                            bom_code = 'FG-' + digits
                        base_text = re.sub(r'(?:\(|\s|_|-)*(fg[-_\s]*\d+|bom[-_\s]*[a-z0-9-]+)(?:\)|\s|_|-)*', '', clean_filename, flags=re.IGNORECASE)
                    
                    base_words = base_text.replace('_', ' ').replace('-', ' ').replace('(', ' ').replace(')', ' ')
                    base_words = re.sub(r'\s+', ' ', base_words).strip()
                    title_base = base_words.title()
                    
                    if bom_code:
                        title = f"{title_base} ({bom_code})"
                    else:
                        title = title_base
                        
                    alt = title
                    description = title
                    
                    tokens = [w for w in base_words.lower().split() if w]
                    if bom_code:
                        tokens.append(bom_code)
                    keywords = ", ".join(list(dict.fromkeys(tokens)))
                    
                image_url = relative_path
                parent_relative_dir = os.path.dirname(relative_path)
                if parent_relative_dir == '.':
                    parent_relative_dir = ''
                
                site_url = '?' + parent_relative_dir + '#pid=' + file
                
                image_url_db = image_url.replace('192.168.1.17:81', 'foto.gkr.my.id')
                site_url_db = site_url.replace('192.168.1.17:81', 'foto.gkr.my.id')
                
                cursor.execute("SELECT id FROM gkr_cari WHERE imageUrl LIKE %s LIMIT 1", ('%' + image_url_db,))
                img_exists = cursor.fetchone()
                
                cursor.execute("SELECT id FROM gkr_cari WHERE url LIKE %s LIMIT 1", ('%' + site_url_db,))
                site_exists = cursor.fetchone()
                
                if img_exists or site_exists:
                    out(f"<span style='color: #4db8ff;'>[INFO]</span> <span style='color: #4a9c8f;'>Skip: {title} sudah ada</span><br>")
                else:
                    try:
                        cursor.execute("""
                            INSERT INTO gkr_cari (judul, alt, deskripsi, url, imageUrl, siteUrl, kata_kunci, kode_bom, klik, rusak)
                            VALUES (%s, %s, %s, %s, %s, %s, %s, %s, 0, 0)
                        """, (title, alt, description, site_url_db, image_url_db, site_url_db, keywords, bom_code))
                        items_added += 1
                        out(f"<span style='color: #28a745;'>[SUCCESS]</span> <span style='color: #d4d4d4;'>Menambahkan produk: {title}</span><br>")
                    except Exception as e:
                        out(f"<span style='color: #dc3545;'>[ERROR] Gagal menambah produk: {e}</span><br>")

kesimpulan = f"SELESAI: Berhasil menambahkan {items_added} produk tunggal ke tabel gkr_cari."
out(f"<span style='color: #4db8ff;'>[INFO]</span> <span style='color: #ffffff;'>{kesimpulan}</span><br>")

cursor.execute("SELECT id, imageUrl FROM gkr_cari WHERE imageUrl IS NOT NULL AND imageUrl != ''")
katalog_images = cursor.fetchall()

if katalog_images:
    export_path = '/var/www/gkr_myid/writable/uploads/gkr_katalog.json'
    try:
        with open(export_path, 'w') as f:
            json.dump(katalog_images, f, indent=4)
        out(f"<span style='color: #28a745;'>[AI SYNC]</span> <span style='color: #d4d4d4;'>Berhasil mengekspor {len(katalog_images)} ID ke gkr_katalog.json</span><br>")
    except Exception as e:
        out(f"<span style='color: #dc3545;'>[AI ERROR]</span> <span style='color: #d4d4d4;'>Gagal menulis ke {export_path}. Reason: {e}</span><br>")
else:
    out("<span style='color: #ffc107;'>[AI WARNING]</span> <span style='color: #d4d4d4;'>Database kosong! Ekspor dibatalkan untuk mencegah amnesia AI.</span><br>")

cursor.close()
conn.close()
sys.exit(0)
