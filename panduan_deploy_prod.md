# Panduan Deployment Mesin Pencari AI ke PROD (192.168.1.17)

Panduan ini berisi langkah-langkah komprehensif untuk memigrasikan fitur **Pencarian Visual AI (Image-to-Image)** dan **Layanan Mikro Python (FastAPI)** dari server DEV (192.168.1.4) ke server PROD (192.168.1.17).

> [!WARNING]
> Pastikan Anda menjalankan perintah-perintah ini di terminal SSH server **PROD (192.168.1.17)** dan menggunakan akun `root` (atau didahului dengan `sudo`).

---

## 1. Perbarui Web Backend (CodeIgniter 4)

Langkah pertama adalah menarik (*pull*) kode terbaru CI4 yang sudah berisi antarmuka `Vue.js` dan skrip pendukung AI.

```bash
# 1. Pindah ke direktori web
cd /var/www/gkr_myid

# 2. Ambil pembaruan dari repository Git (cabang dev/main)
git pull origin dev

# 3. Sesuaikan hak akses agar web server bisa membaca aset baru
chown -R www-data:www-data /var/www/gkr_myid
find /var/www/gkr_myid -type d -exec chmod 755 {} \;
find /var/www/gkr_myid -type f -exec chmod 644 {} \;
```

---

## 2. Bangun Fondasi Layanan Mikro AI (Python)

Kita akan membuat lingkungan terisolasi di direktori yang sama persis (`/mnt/sdcard/ai-scanner/`) seperti di server Dev.

```bash
# 1. Buat dan masuki direktori kerja AI Scanner
mkdir -p /mnt/sdcard/ai-scanner
cd /mnt/sdcard/ai-scanner/

# 2. Pastikan pustaka virtual environment Python terpasang di OS
apt-get update
apt-get install python3-venv python3-pip -y

# 3. Buat Virtual Environment khusus (agar tidak bentrok dengan sistem)
python3 -m venv env-ai
```

---

## 3. Salin Jantung Kecerdasan Buatan (Script AI)

Pindahkan skrip-skrip inti mesin *Machine Learning* yang sudah Anda *pull* di folder CI4 ke dalam kandang AI.

```bash
# 1. Salin Router FastAPI
cp /var/www/gkr_myid/python_services/main_new.py /mnt/sdcard/ai-scanner/main.py

# 2. Salin Mesin Pemeras Ekstraksi Vektor (FAISS)
cp /var/www/gkr_myid/python_services/build_index_new.py /mnt/sdcard/ai-scanner/build_index.py

# 3. Salin Daftar Depedensi (Requirements)
cp /var/www/gkr_myid/python_services/req.txt /mnt/sdcard/ai-scanner/req.txt
```

---

## 4. Unduh "Otak" Artificial Intelligence

Langkah ini akan mengunduh model **MobileNetV3 (PyTorch)**, **FAISS**, dan pelayan mikro HTTP **FastAPI**. Proses ini mungkin akan memakan waktu 2-5 menit tergantung kecepatan internet server.

```bash
# 1. Masuk ke ruang isolasi Python
source env-ai/bin/activate

# 2. Instal semua pustaka dari req.txt
pip install -r req.txt

# 3. Keluar dari ruang isolasi jika sudah selesai
deactivate
```

---

## 5. Bangun Layanan Otomatis (Systemd Daemon)

Layanan ini memastikan API FastAPI berjalan di balik layar (secara gaib) di port 5000, serta otomatis menyala setiap kali peladen (server) PROD di-*reboot*.

1. Buka editor teks untuk membuat berkas layanan baru:
```bash
nano /etc/systemd/system/ai_scanner.service
```

2. Salin dan tempel (Paste) konfigurasi sakti ini:
```ini
[Unit]
Description=Mesin Pencari Visual Gracia (FastAPI Microservice)
After=network.target

[Service]
User=root
WorkingDirectory=/mnt/sdcard/ai-scanner
ExecStart=/mnt/sdcard/ai-scanner/env-ai/bin/python3 -m uvicorn main:app --host 127.0.0.1 --port 5000
Restart=always

[Install]
WantedBy=multi-user.target
```
*(Tekan `CTRL+X`, lalu `Y`, lalu `Enter` untuk menyimpan di Nano).*

3. Aktifkan dan jalankan layanannya:
```bash
systemctl daemon-reload
systemctl enable ai_scanner.service
systemctl start ai_scanner.service

# Pastikan status berwarna hijau (active running)
systemctl status ai_scanner.service
```

---

## 6. Sinkronisasi Perdana (First-time Indexing)

> [!IMPORTANT]
> Fitur pencarian gambar Anda **belum bisa digunakan** karena AI belum memiliki "Ingatan Visual" (*produk.index* dan *produk_mapping.json*). 

Anda bisa membangun ingatan perdananya dengan dua cara:

**Opsi A: Menggunakan UI Web (Sangat Disarankan)**
1. Buka peramban web dan arahkan ke alamat PROD Anda: `http://192.168.1.17/crawl/ai`
2. Klik tombol **Mulai Pelatihan AI**.
3. Tunggu hingga terminal *live streaming* menyemprotkan status **[SELESAI]**.

**Opsi B: Menggunakan SSH Manual**
Jika Anda ingin melihatnya bekerja dari terminal SSH *root* server PROD:
```bash
cd /mnt/sdcard/ai-scanner
./env-ai/bin/python3 -u build_index.py
```

Selamat! Sistem Kecerdasan Buatan (AI) Anda telah resmi mendarat dengan aman di peladen Produksi!
