import os
import sys
import re
import datetime

def jalankan_scanner():
    print("=== Script Scanner Teks Direktori (Armbian Linux / Root) ===\n")
    
    # Lokasi direktori tempat script ini berada
    SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
    
    # Batas ukuran file 25 MB dalam Byte
    BATAS_UKURAN_BYTE = 25 * 1024 * 1024 
    
    # Rekomendasi: Daftar ekstensi file (Media/Biner) yang DILEWATI/SKIP
    # Teks, PHP, JS, Vue, HTML, dsb tetap akan di-scan.
    EKSTENSI_DILEWATI = (
        # Gambar & Desain
        '.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg', '.webp', '.ico', '.psd',
        # Video & Audio
        '.mp4', '.mov', '.avi', '.mkv', '.flv', '.wmv', '.mp3', '.wav', '.ogg',
        # Biner, Arsip & Dokumen Non-Teks
        '.zip', '.rar', '.tar.gz', '.gz', '.7z', '.pdf', '.exe', '.dll', '.so', '.sqlite', '.db'
    )
    
    while True:
        # 1. Menerima input teks yang dicari
        teks_cari = input("Masukkan teks UTUH yang dicari (misal: gkr_cari): ").strip()
        if not teks_cari:
            print("Teks tidak boleh kosong. Silakan coba lagi.\n")
            continue
            
        # Membuat pola Regex untuk EXACT MATCH (Pencarian Tepat & Case-Insensitive)
        # \b memastikan kata berdiri sendiri, re.IGNORECASE mengabaikan besar/kecil huruf
        pola_regex = re.compile(r'\b' + re.escape(teks_cari) + r'\b', re.IGNORECASE)

        # 2. Menerima input direktori
        direktori = input("Masukkan direktori utama (misal: /var/www/gkr_myid): ").strip()
        if not os.path.exists(direktori):
            print(f"Direktori '{direktori}' tidak ditemukan. Silakan periksa kembali.\n")
            continue

        # 3. Menerima input opsi direktori yang ingin dilewati (skip)
        input_skip = input("Masukkan nama folder yang DILEWATI (pisahkan dengan koma, misal: vendor, writable | Kosongkan jika scan semua): ").strip()
        daftar_skip = [d.strip() for d in input_skip.split(',')] if input_skip else []

        # 4. Konfirmasi
        konfirmasi = input(f"Mulai telusuri kata utuh '{teks_cari}' di seluruh '{direktori}'? (Ya / Tidak): ").strip().lower()

        if konfirmasi in ['ya', 'y']:
            print("\nMelakukan scanning (Symlink Protection Aktif), harap tunggu...\n")
            
            hasil_pencarian = []
            file_besar_dilewati = [] 
            file_media_dilewati = 0
            
            # Set (himpunan) untuk mencegah Symlink Infinite Loop
            visited_dirs = set()
            
            # Melakukan perulangan dengan followlinks=True
            for root, dirs, files in os.walk(direktori, followlinks=True):
                
                # --- LOGIKA PENCEGAH INFINITE LOOP SYMLINK ---
                real_root = os.path.realpath(root)
                if real_root in visited_dirs:
                    dirs[:] = [] # Kosongkan daftar sub-folder agar os.walk berhenti masuk lebih dalam
                    continue
                visited_dirs.add(real_root)
                
                # --- LOGIKA SKIP FOLDER (Input User) ---
                if daftar_skip:
                    dirs[:] = [d for d in dirs if d not in daftar_skip]

                for file in files:
                    # --- LOGIKA SKIP FILE MEDIA & BINER ---
                    if file.lower().endswith(EKSTENSI_DILEWATI):
                        file_media_dilewati += 1
                        continue
                        
                    file_path = os.path.join(root, file)
                    
                    try:
                        # Cek ukuran file (> 25MB)
                        ukuran_file = os.path.getsize(file_path)
                        if ukuran_file > BATAS_UKURAN_BYTE:
                            file_besar_dilewati.append((file_path, ukuran_file))
                            continue 
                            
                        # Buka file dan periksa Regex Exact Match
                        with open(file_path, 'r', encoding='utf-8', errors='ignore') as f:
                            for baris in f:
                                # Jika regex cocok (Exact Match) pada baris ini
                                if pola_regex.search(baris):
                                    hasil_pencarian.append(file_path)
                                    break 
                    except Exception:
                        pass
            
            # --- PENYUSUNAN NAMA FILE OTOMATIS (Timestamp & Index) ---
            tanggal_hari_ini = datetime.datetime.now().strftime("%Y%m%d")
            index = 1
            while True:
                nama_file_output = f"hasil_scan_{tanggal_hari_ini}_{index}.md"
                path_output = os.path.join(SCRIPT_DIR, nama_file_output)
                if not os.path.exists(path_output):
                    break
                index += 1
            
            # --- MULAI MENAMPILKAN & MENYIMPAN HASIL ---
            total_ditemukan = len(hasil_pencarian)
            
            print(f"Ditemukan ({total_ditemukan}), pada direktori:")
            if total_ditemukan > 0:
                for path in hasil_pencarian:
                    print(f" - {path}")
            else:
                print(f" (Tidak ada file yang mengandung kata persis '{teks_cari}')")
                
            print(f"\n[Scanner Python3 Info] File media/biner di-skip: {file_media_dilewati} file")
            if len(file_besar_dilewati) > 0:
                print(f"[Scanner Python3 Info] File > 25 MB di-skip: {len(file_besar_dilewati)} file")
            
            # Menyimpan ke file .md sejajar dengan script
            try:
                with open(path_output, 'w', encoding='utf-8') as f_out:
                    f_out.write(f"# Hasil Scanning Teks (Exact Match)\n\n")
                    f_out.write(f"- **Teks yang dicari:** `{teks_cari}` (Utuh / Exact Match)\n")
                    f_out.write(f"- **Direktori target:** `{direktori}`\n")
                    f_out.write(f"- **Folder dilewati:** {', '.join(daftar_skip) if daftar_skip else '(Tidak ada)'}\n")
                    f_out.write(f"- **Total ditemukan:** {total_ditemukan} file\n\n")
                    
                    f_out.write("## Daftar File Ditemukan\n")
                    if total_ditemukan > 0:
                        for path in hasil_pencarian:
                            f_out.write(f"- {path}\n")
                    else:
                        f_out.write("*Tidak ada file yang cocok.*\n")
                        
                    if len(file_besar_dilewati) > 0:
                        f_out.write(f"\n## Scanner Info: File > 25 MB (Dilewati)\n")
                        for path, ukuran in file_besar_dilewati:
                            f_out.write(f"- {path} ({(ukuran / (1024*1024)):.2f} MB)\n")
                            
                print(f"\n[SUKSES] Laporan disimpan di: {path_output}\n")
            except Exception as e:
                print(f"\n[GAGAL] Tidak dapat menyimpan file. Error: {e}\n")

            continue 
            
        elif konfirmasi in ['tidak', 't', 'no', 'n']:
            print("\nProses dibatalkan. Kembali ke prompt awal...\n")
            continue
        else:
            print("\nPilihan tidak valid. Kembali ke prompt awal...\n")
            continue

if __name__ == "__main__":
    try:
        jalankan_scanner()
    except KeyboardInterrupt:
        print("\n\nProgram dihentikan oleh user (Ctrl+C). Keluar...")
        sys.exit(0)