# Panduan Lengkap: Operasional & Eksekusi AI Trainer Engine (PROD)

Dokumen ini merupakan referensi lengkap yang memuat langkah-langkah pengelolaan, pelatihan ulang (re-indexing), serta operasional dasar untuk sistem **AI Scanner / AI Trainer Engine** pada server Produksi (192.168.1.17).

> [!TIP]
> **Arsitektur Sistem**
> Seluruh skrip manajerial berada di `/var/www/gkr_myid/python_services/`, sedangkan _service_ utama yang memuat model Machine Learning (MobileNetV3) berlokasi di `/mnt/sdcard/ai-scanner/`. Keduanya dijalankan menggunakan lingkungan terisolasi (*virtual environment*) `/mnt/sdcard/ai-scanner/env-ai/`.

---

## 1. Pembaruan Indeks FAISS (Training/Re-indexing)
Setiap kali ada penambahan, penghapusan, atau perubahan massal pada pangkalan gambar di direktori `/var/www/FOTO` (seperti katalog `BUYER`, `GRACIA`, dll), Anda harus melatih ulang basis data vektor (`produk.index`) agar mesin pencari gambar dapat mengenali foto-foto baru tersebut.

**Langkah Eksekusi:**
```bash
# 1. Pindah ke direktori skrip pengelolaan
cd /var/www/gkr_myid/python_services

# 2. Jalankan skrip pembangun indeks (menggunakan virtual environment)
/mnt/sdcard/ai-scanner/env-ai/bin/python3 build_index_new.py
```
> [!NOTE]
> Proses ini memakan waktu bergantung pada seberapa banyak gambar yang dipindai. Hasil akhir berupa fail `produk.index` (Pangkalan Vektor FAISS) dan `mapping.json` akan ditimpa (diperbarui) di folder kerja saat ini.

---

## 2. Sinkronisasi pHash (Perceptual Hashing)
Jika mesin *crawler* PHP telah menarik URL gambar baru ke dalam database `cari_images`, Anda harus menyalakan skrip *seeder/crawler* Python untuk mengunduh gambar ke RAM dan menyuntikkan `image_hash` ke dalam tabel MySQL.

**Langkah Eksekusi:**
```bash
# Pindah ke direktori layanan
cd /var/www/gkr_myid/python_services

# Eksekusi seeder (menggunakan Python global atau environment yang memiliki modul mysql-connector)
python3 crawler_seeder.py
```
> [!IMPORTANT]
> Skrip ini menggunakan kredensial statis (hardcoded) yang aman dan otomatis akan mengabaikan gambar yang nilai `image_hash`-nya sudah terisi, sehingga tidak akan membebani _server_.

---

## 3. Manajemen Systemd Services (Menghidupkan/Mematikan API)
Sistem ini disokong oleh dua layanan utama di balik layar yang berjalan secara asinkron.

- `ai_scanner.service`: API FastAPI (Port 5000) yang memuat model AI (MobileNetV3).
- `image_search.service`: API bantu untuk sinkronisasi sekunder.

### Cek Status Layanan (Monitoring)
Gunakan perintah ini untuk melihat apakah layanan berjalan lancar atau melihat *error* terbaru:
```bash
systemctl status ai_scanner.service
systemctl status image_search.service
```

### Restart Layanan (Wajib setelah re-indexing)
Setelah Anda menjalankan skrip `build_index_new.py`, mesin AI harus memuat ulang fail `.index` yang baru ke dalam memori RAM. Anda **wajib** melakukan restart _service_:
```bash
systemctl restart ai_scanner.service
```

### Memantau Log secara *Real-time* (*Live Stream*)
Untuk memantau permintaan *HTTP* yang masuk ke AI Engine ketika pengguna (di aplikasi web Vue.js) melakukan pencarian berbasis gambar:
```bash
journalctl -u ai_scanner.service -f
```

---

## 4. Rangkuman Struktur Folder Kunci
- `/var/www/FOTO`: Lokasi mutlak di mana semua citra (gambar) mentah disimpan (termasuk folder `BUYER`, `WEB`, `GRACIA`).
- `/var/www/gkr_myid/python_services`: Lokasi skrip utilitas harian (contoh: `build_index_new.py`, `crawler_seeder.py`).
- `/mnt/sdcard/ai-scanner`: Direktori inti tempat *source code* utama FastAPI (`main.py`) dan *Virtual Environment* Python bertempat. Fail `ai_scanner.service` menunjuk ke path ini sebagai titik awal eksekusi.
