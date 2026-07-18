# Peta Jalan Pengembangan: Inovasi AI Berbasis MobileNetV3 & FAISS

Dengan arsitektur yang Anda miliki saat ini (*PyTorch MobileNetV3* sebagai mesin pengekstraksi fitur dan *FAISS* sebagai *database* pencarian matriks), Anda sebenarnya sudah memiliki pondasi **Visual Search Engine** berkelas *enterprise*! 

Kombinasi ini tidak hanya sebatas untuk fitur "Pencarian Gambar" (seperti Google Lens). Berikut adalah 5 inovasi brilian yang bisa Anda bangun selanjutnya tanpa harus mengganti infrastruktur server (STB) Anda:

---

## 1. Rekomendasi "Produk Serupa" (Visual Recommendation)
Fitur ini sangat esensial untuk mendongkrak penjualan (Upselling).
- **Cara Kerja:** Ketika pelanggan membuka halaman detail `Kursi Jati Tipe A`, sistem akan mengambil *vektor* Kursi A dari FAISS, lalu mencari tetangga terdekatnya (Nearest Neighbors). 
- **Hasil:** Halaman web secara otomatis akan memunculkan *widget*: *"Mungkin Anda juga menyukai..."* dan menampilkan Kursi B, Kursi C, dan Kursi D yang memiliki gaya lekukan kayu atau bentuk 3D yang sangat mirip dengan Kursi A.
- **Kelebihan:** 100% otomatis. Anda tidak perlu memasukkan tag "kursi jati", "kursi ukir", secara manual ke *database*. AI akan mengenalinya dari kemiripan visual.

## 2. Pencocokan Material & Kain (Swatch Matcher)
Saya perhatikan Anda memiliki folder `/SWATCHES`.
- **Cara Kerja:** Pelanggan bisa memfoto gorden ruang tamu mereka, atau lantai kayu rumah mereka, lalu mengunggahnya. AI akan mengukur warna, serat kayu, atau pola jahitan kain.
- **Hasil:** Sistem akan merekomendasikan furnitur atau jenis bantalan kain (*fabric swatch*) yang paling menyatu/cocok dengan warna ruangan tersebut berdasarkan kedekatan jarak vektor di FAISS.

## 3. Crop Pintar (Auto-Crop) dengan YOLO
Saat ini, jika pelanggan memfoto seluruh ruang tamu, AI `MobileNetV3` mungkin kebingungan karena melihat sofa, TV, meja, dan karpet sekaligus dalam satu foto.
- **Pengembangan:** Tambahkan model pelacak objek super ringan (seperti *YOLOv8 Nano*). 
- **Cara Kerja:** Saat foto masuk, YOLO memotong (*crop*) khusus bagian kursi/mejanya saja, lalu potongan gambar tersebut baru diserahkan ke `MobileNetV3` dan FAISS. Akurasi pencarian dari foto asli (keadaan ruangan) akan meningkat drastis!

## 4. Klasifikasi Kategori Otomatis (Auto-Tagging)
Ketika tim admin Anda mengunggah produk baru, mereka sering kali lupa menuliskan kategori, warna, atau *style* (Minimalis, Klasik, dll).
- **Cara Kerja:** Setiap gambar baru diunggah, lempar ke FAISS. Lihat 5 gambar terdekatnya. Jika kelima gambar terdekatnya memiliki tag "Sofa Klasik", maka produk baru ini akan otomatis diberi label "Sofa Klasik" oleh sistem!

## 5. Sinkronisasi Vektor Berbasis Edge (Smartphone)
Karena Anda menggunakan `MobileNetV3` (yang diciptakan khusus untuk *Mobile/Edge Device*):
- **Pengembangan Jangka Panjang:** Model PyTorch ini bisa diubah ke format TFLite dan ditanam langsung ke dalam aplikasi *smartphone* pelanggan. 
- **Keuntungan:** Proses pengubahan gambar menjadi Vektor 3D tidak lagi membebani *server* STB Anda, melainkan diproses langsung oleh HP pelanggan! HP pelanggan hanya perlu mengirim teks angka (Matriks Vektor) ke *server* Anda. Beban server Anda akan menjadi nyaris 0%, dan pencarian akan terasa secepat kilat (Instant Search).

---

> [!TIP] 
> **Rekomendasi Utama untuk Dikerjakan Selanjutnya:**
> Fitur **Rekomendasi "Produk Serupa"** adalah yang paling mudah dan paling cepat untuk diimplementasikan saat ini, karena Anda sama sekali tidak perlu melatih AI baru. Anda cukup memanggil fungsi `index_faiss.search(vektor_produk_ini)` pada halaman detail produk di website Anda.
