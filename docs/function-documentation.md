# Dokumentasi Fungsi dan Prosedur Sistem Neston

---

## Definisi Teknis

Dalam dokumentasi ini, kami membagi metode menjadi dua kategori utama sesuai standar rekayasa perangkat lunak:
- **Fungsi (Function):** Metode yang tujuan utamanya adalah mengambil data, melakukan perhitungan, atau menampilkan informasi tanpa mengubah status sistem secara signifikan.
- **Prosedur (Procedure):** Metode yang tujuan utamanya adalah melakukan serangkaian aksi yang mengubah status data di database (seperti menyimpan, mengubah, atau menghapus).

---

## 1. Modul Transaksi & Operasional Parkir (`TransaksiController`)

| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `index()` | **Fungsi** | Mengambil dan menampilkan daftar transaksi terfilter. |
| `create()` | **Fungsi** | Menampilkan form input data parkir. |
| `checkIn()` | **Prosedur** | Melakukan serangkaian aksi: validasi, simpan data kendaraan, buat transaksi, dan update kapasitas area. |
| `checkOut()` | **Prosedur** | Menghitung durasi/biaya dan memperbarui status transaksi menjadi keluar. |
| `show()` | **Fungsi** | Mengambil detail satu data transaksi. |
| `edit()` | **Fungsi** | Menampilkan form untuk pengeditan data. |
| `update()` | **Prosedur** | Menyimpan perubahan data ke database. |
| `destroy()` | **Prosedur** | Menghapus record transaksi (Soft Delete). |
| `print()` | **Fungsi** | Menghasilkan format visual struk untuk dicetak. |
| `acceptReservation()` | **Prosedur** | Mengubah status reservasi menjadi transaksi aktif. |
| `rejectReservation()` | **Prosedur** | Membatalkan reservasi dan menghapus slot terkait. |

---

## 2. Modul Pembayaran & Keuangan (`PaymentController` & `CashPaymentController`)

### `PaymentController` (Online & Umum)
| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `selectTransaction()` | **Fungsi** | Mencari transaksi yang siap dibayar. |
| `create()` | **Fungsi** | Menampilkan halaman pemilihan metode pembayaran. |
| `midtransPay()` | **Fungsi** | Menyiapkan halaman integrasi Midtrans. |
| `midtransSnapToken()` | **Fungsi** | Mengambil token sesi pembayaran dari API Midtrans. |
| `midtransNotification()`| **Prosedur** | Memproses notifikasi webhook dan memperbarui status pembayaran di database. |
| `success()` | **Fungsi** | Menampilkan konfirmasi keberhasilan. |
| `userBills()` | **Fungsi** | Menampilkan daftar tagihan user. |

### `CashPaymentController` (Manajemen Kasir)
| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `openShift()` | **Prosedur** | Membuka sesi kasir dan mencatat saldo awal. |
| `closeShift()` | **Prosedur** | Menutup sesi kasir dan merekap total pendapatan tunai. |
| `confirm()` | **Prosedur** | Memvalidasi penerimaan uang tunai dan melunasi tagihan. |
| `dailyBreakdown()` | **Fungsi** | Menghitung rekapitulasi pendapatan harian. |

---

## 3. Modul Area & Peta Interaktif (`AreaParkirController` & `ParkingSlotController`)

### `AreaParkirController` (Manajemen Master)
| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `store()` | **Prosedur** | Menyimpan data area parkir baru. |
| `design()` | **Fungsi** | Membuka antarmuka editor peta. |
| `saveDesign()` | **Prosedur** | Menyimpan koordinat koordinat visual slot ke database. |

### `ParkingSlotController` (Interaksi User)
| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `view()` | **Fungsi** | Menampilkan peta visual real-time. |
| `index()` (API) | **Fungsi** | Menyediakan data status slot dalam format JSON. |
| `bookmark()` | **Prosedur** | Mengunci slot selama 10 menit untuk reservasi user. |
| `unbookmark()` | **Prosedur** | Melepas kunci slot yang sebelumnya dibookmark. |

---

## 4. Modul Teknologi & ANPR (`ANPRController` & `PlateRecognizerController`)

| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `scanPlate()` | **Fungsi** | Mengirim gambar ke sistem pengenalan dan mengembalikan hasil teks plat nomor. |
| `handleDetection()` | **Prosedur** | Memproses deteksi kamera (otomatis Check-in/Out sesuai status). |
| `normalize()` | **Fungsi** | Mengolah string plat nomor menjadi format standar. |

---

## 5. Modul RFID & Akses (`RfidParkingController` & `RfidAccessController`)

| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `processScan()` | **Prosedur** | Menentukan alur (In/Out) saat kartu ditap dan memprosesnya. |
| `saveRfid()` | **Prosedur** | Menautkan UID kartu ke profil user di database. |
| `identify()` | **Fungsi** | Mencari data pemilik berdasarkan UID kartu. |
| `scan()` (Access) | **Prosedur** | Memvalidasi hak akses dan menggerakkan gate/pintu. |

---

## 6. Modul Manajemen User & Saldo (`UserController` & `SaldoController`)

### `UserController`
| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `dashboard()` | **Fungsi** | Menampilkan statistik dan data user. |
| `topup()` (Admin) | **Prosedur** | Menambahkan saldo ke akun user secara manual. |

### `SaldoController` (NestonPay)
| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `storeTopupManual()`| **Prosedur** | Mencatat pengajuan top-up saldo user. |
| `processPayWithSaldo()`| **Prosedur** | Melakukan pemotongan saldo user untuk membayar parkir. |

---

## 7. Modul Pelaporan & Rekonsiliasi (`ReportController` & `RevenueReconciliationController`)

| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `pembayaran()` | **Fungsi** | Menampilkan data rekap pembayaran. |
| `transaksi()` | **Fungsi** | Menampilkan data rekap jumlah kendaraan. |
| `exportPembayaranCSV()`| **Prosedur** | Menghasilkan dan mengunduh file laporan (proses ekspor). |
| `syncMissingPayments()`| **Prosedur** | Sinkronisasi data pembayaran Midtrans yang belum tercatat otomatis. |

---

## 8. Modul Konfigurasi Sistem (`TarifController`, `CameraController`, `LogAktifitasController`)

### `TarifController`
| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `store()` / `update()` | **Prosedur** | Mengelola besaran tarif parkir per jam untuk tiap jenis kendaraan. |

### `CameraController`
| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `store()` / `update()` | **Prosedur** | Mendaftarkan perangkat kamera (IP Webcam/Scanner) ke sistem. |

### `LogAktifitasController`
| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `index()` | **Fungsi** | Menampilkan seluruh riwayat audit trail sistem. |
| `deleteAll()` | **Prosedur** | Membersihkan data log lama untuk optimasi database. |

---

## 9. Modul Kendaraan & User (`KendaraanController` & `UserVehicleController`)

### `KendaraanController` (Admin)
| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `store()` | **Prosedur** | Mendaftarkan data kendaraan baru secara manual oleh Admin. |

### `UserVehicleController` (Member)
| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `index()` | **Fungsi** | Menampilkan daftar kendaraan milik user yang sedang login. |
| `store()` | **Prosedur** | Mendaftarkan kendaraan pribadi user untuk kemudahan check-in. |

---

## 10. Modul Layanan & Pendukung (`Services` & `Support`)

| Nama Metode | Jenis | Deskripsi |
| :--- | :--- | :--- |
| `scanPlate()` | **Fungsi** | Mengembalikan hasil pembacaan plat nomor dari gambar. |
| `logActivity()` | **Prosedur** | Melakukan aksi penulisan ke tabel log sistem. |
| `sendMessage()` | **Prosedur** | Melakukan aksi pengiriman pesan melalui gateway WhatsApp. |
| `getDurasiJam()` | **Fungsi** | Menghitung durasi waktu dan mengembalikan nilai angka. |

---
*Dokumentasi ini dibuat secara otomatis berdasarkan struktur kode program Neston per April 2026.*
