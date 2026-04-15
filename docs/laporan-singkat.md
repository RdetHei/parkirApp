# Laporan Singkat Proyek NESTON (Smart Parking System)

Laporan ini merangkum status terkini proyek per April 2026, mencakup fitur yang sudah stabil, kendala teknis yang masih dihadapi, serta visi pengembangan di masa mendatang.

---

## a. Fitur yang sudah berjalan dengan baik

- **Sistem Autentikasi & RBAC**: Mekanisme login, registrasi, verifikasi email, dan pembatasan akses berdasarkan role (Admin, Petugas, Owner, User) sudah berjalan stabil.
- **Manajemen Master Data**: Pengelolaan data area parkir, tarif, kamera, dan user sudah terintegrasi penuh.
- **Operasional Parkir Utama**: Alur check-in/out kendaraan, pencatatan durasi parkir otomatis, dan perhitungan biaya berdasarkan tarif per jam sudah fungsional.
- **Peta Parkir Interaktif**: Visualisasi real-time status slot (kosong, terisi, booking) beserta fitur reservasi sementara (10 menit) sudah dapat digunakan.
- **Integrasi ANPR (OCR)**: Pemindaian plat nomor otomatis menggunakan sistem pengenalan plat terbaru untuk mempercepat proses check-in/out.
- **Sistem Pembayaran Omni**: Dukungan pembayaran digital via Midtrans (Gopay, VA, dll), sistem saldo internal (NestonPay), dan pembayaran tunai melalui kasir.
- **Notifikasi Otomatis**: Pengiriman bukti transaksi dan tagihan secara otomatis melalui Email dan WhatsApp Gateway.

---

## b. Bug yang belum diperbaiki (Identifikasi Potensial)

- **Ketergantungan API Eksternal**: Sistem masih sangat bergantung pada ketersediaan layanan pengenalan plat dan WhatsApp Gateway. Diperlukan mekanisme *offline fallback* yang lebih kuat.
- **Sinkronisasi Webhook Midtrans**: Pada lingkungan pengembangan (*localhost*), terkadang notifikasi sukses dari Midtrans terhambat oleh masalah jaringan/tunneling.
- **Penanganan Plat Nomor Duplikat**: Meskipun sudah ada normalisasi, diperlukan validasi lebih ketat untuk menangani kasus plat nomor yang sama terdeteksi hampir bersamaan di dua titik area berbeda.
- **Optimasi Mobile View**: Beberapa bagian dari peta parkir interaktif masih memerlukan penyesuaian agar lebih responsif di perangkat layar kecil.

---

## c. Rencana Pengembangan Berikutnya

- **Aplikasi Mobile (Native/Hybrid)**: Mengembangkan aplikasi khusus untuk user agar dapat melakukan booking slot dan manajemen saldo dengan lebih praktis.
- **Integrasi IoT (Hardware Integration)**: Menghubungkan sistem langsung ke palang pintu parkir (barrier gate) melalui mikrokontroler untuk pembukaan otomatis.
- **Analitik & Business Intelligence**: Penambahan fitur grafik tren pendapatan bulanan dan statistik okupansi area untuk membantu Owner dalam pengambilan keputusan.
- **Sistem Membership & Langganan**: Fitur parkir berlangganan (bulanan/mingguan) dengan sistem pembayaran otomatis untuk pengguna tetap.
- **Enhanced Security**: Penambahan fitur pengenalan wajah untuk verifikasi tambahan saat check-out kendaraan member.

---
*Laporan ini disusun secara otomatis berdasarkan audit sistem per 15 April 2026.*
