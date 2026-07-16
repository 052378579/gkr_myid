# Rencana Implementasi: Integrasi AI Scanner (MobileNetV3 + FAISS)

Sistem pencarian gambar saat ini (*Hamming Distance* / pHash) akan dinonaktifkan dan digantikan sepenuhnya oleh **AI Scanner** (Deep Learning) yang berlokasi di `/mnt/sdcard/ai-scanner`.

## User Review Required
> [!IMPORTANT]
> Port standar FastAPI adalah 8000, namun karena `gkr_myid` telah menggunakannya, kita akan **menggunakan port `8001`** untuk servis AI Scanner ini agar tidak terjadi *port collision* (bentrok).

## Proposed Changes

---

### Konfigurasi Service AI (FastAPI)

#### [NEW] [ai_scanner.service](file:///var/www/gkr_myid/python_services/ai_scanner.service)
- Membuat berkas *Systemd Service* baru khusus untuk FastAPI agar dapat berjalan secara otomatis dan permanen di *background*.
- Service ini akan memanggil `uvicorn main:app --host 127.0.0.1 --port 8001` dari direktori `/mnt/sdcard/ai-scanner`.

---

### Backend CodeIgniter 4 (PHP)

#### [MODIFY] [ImageSearchApi.php](file:///192.168.1.4/www/gkr_myid/app/Controllers/ImageSearchApi.php)
- Mengubah arah POST *request cURL* dari port 5000 (sistem lama) menjadi port **8001** (endpoint `/scan`).
- Menangkap nilai *return* JSON dari FastAPI berupa sekumpulan key: `{"status": "success", "kode_bom": "FG-...", "confidence": 0.8}`.
- Menyimpan nilai `kode_bom` ke dalam Session PHP (`search_kode_bom`) alih-alih hash panjang, dan menyertakan nilai `confidence` (tingkat akurasi) ke sesi jika diperlukan.

#### [MODIFY] [Search.php](file:///192.168.1.4/www/gkr_myid/app/Controllers/Search.php)
- Pada blok logika `$tipe === 'image_results'`, sistem tidak lagi menggunakan query kompleks `BIT_COUNT()`.
- Mengganti logika SQL menjadi lebih efisien dengan melakukan filter teks pencarian berdasarkan `kode_bom` yang disuplai oleh AI:
  ```php
  $modelGambar->groupStart()
              ->like('title', $kodeBom)
              ->orLike('imageUrl', $kodeBom)
              ->groupEnd()
              ->where('broken', 0);
  ```
- Ini menjamin bahwa gambar-gambar yang dirender pada UI benar-benar merujuk pada produk furnitur yang sama persis sesuai pengenalan visual mesin.

#### [MODIFY] [search_results.php](file:///192.168.1.4/www/gkr_myid/app/Views/search_results.php)
- Memperbarui teks *feedback* pada antarmuka, dari "Menampilkan hasil pencarian gambar serupa" menjadi keterangan lebih pintar seperti "AI mengenali produk ini sebagai: **[Kode BOM]** (Kemiripan: **XX%**)".

---

## Verification Plan

### Manual Verification
1. Masuk ke terminal Armbian, matikan layanan *hash* lama (`systemctl stop image_search.service`).
2. Aktifkan layanan AI baru (`systemctl start ai_scanner.service`).
3. Buka situs dari peramban (browser), unggah foto kursi ke kolom pencarian.
4. Verifikasi bahwa halaman hasil pencarian berhasil memuat secara instan dan hanya menampilkan variasi gambar produk dengan `Kode BOM` yang tepat.
