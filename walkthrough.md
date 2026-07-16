# Integrasi Ekstensi "Kecerdasan AI" untuk Corak & Tekstur

Kini mesin AI Anda (*MobileNetV3* & *FAISS*) bukan hanya andal dalam mengenali kursi, tetapi juga sangat akurat dalam mengenali jenis material, kain, serta corak rotan/besi (Swatch)!

## Apa yang telah berubah?

### 1. Jangkauan Pemindaian (Training) Raksasa
Sebelumnya AI hanya membaca foto produk utuh di folder `GRACIA/2026/`. Kini, otak AI (skrip `build_index.py`) telah mengekstrak fitur visual dari **3.495 gambar** yang tersebar secara rekursif di:
- `FOTO/BUYER/`
- `FOTO/GRACIA/`
- `FOTO/SAMPLE GRACIA/`
- `FOTO/SWATCHES/`

Dari ribuan file tersebut, ia berhasil menyeleksi dan memasukkan **145 produk & corak** ke dalam indeks vektor (*database* otak AI).

### 2. Logika "Pendeteksi Corak" di Web UI
Ketika sebuah foto corak/swatch diunggah, AI akan mendeteksinya. Karena foto corak biasanya dipotret secara bervariasi (terkadang *zoom-in*, *zoom-out*, dengan pencahayaan berbeda), saya telah menurunkan ambang batas kemiripan (*threshold*) dari **72%** menjadi **68%** khusus untuk melenturkan kecerdasan AI.

Ketika AI menyimpulkan bahwa gambar tersebut adalah sebuah corak/swatch (bukan kursi utuh), sistem web (CodeIgniter) akan mendeteksinya dan **tidak akan memaksakan query pencarian ke *database* SQL**. Sebaliknya, layar pencarian akan dengan cerdas menampilkan nama bahan aslinya:
> <i class="fa-solid fa-wand-magic-sparkles text-warning"></i> AI mengenali corak/material ini sebagai: **Agora Liso Acqua** 

## Cara Verifikasi
1. Buka halaman web Anda dan klik ikon **Kamera** di baris pencarian.
2. Unggah kembali corak/tekstur kain yang sebelumnya gagal dikenali (muncul notifikasi merah).
3. Voila! Notifikasi *error* merah kini akan menghilang, AI akan berhasil memindai coraknya, dan Anda akan dialihkan ke layar pencarian yang menyebutkan nama asli dari material tersebut.
