# Dokumentasi Modular NESTON Smart Parking System

## Pendahuluan
Dokumentasi ini memuat spesifikasi lengkap setiap modul dalam sistem NESTON Smart Parking, meliputi: **Input** (data masuk), **Proses** (alur kerja), **Output** (hasil), serta **Fungsi, Prosedur, dan Method** yang digunakan.

---

## Daftar Modul

1. [Modul Parking Entry-Exit (ANPR & RFID)](#modul-1-parking-entry-exit-anpr--rfid)
2. [Modul Kendaraan](#modul-2-kendaraan)
3. [Modul Pembayaran](#modul-3-pembayaran)
4. [Modul Area Parkir & Slot](#modul-4-area-parkir--slot)
5. [Modul User & Authentication](#modul-5-user--authentication)
6. [Modul Dashboard & Report](#modul-6-dashboard--report)
7. [Modul Konfigurasi & Utility](#modul-7-konfigurasi--utility)
8. [Modul API External](#modul-8-api-external)

---

## Modul 1: Parking Entry-Exit (ANPR & RFID)

### 1.1 ANPRController

**Lokasi**: `app/Http/Controllers/ANPRController.php`

#### 1.1.1 Method: `handleDetection`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request` berisi: `plate` (string), `confidence` (numeric), `vehicle_type`, `vehicle_color`, `vehicle_make`, `vehicle_model`, `image` (file opsional) |
| **Proses** | 1. Validasi confidence threshold (min 0.8). 2. Upload gambar ke Cloudinary. 3. Normalisasi plat nomor. 4. Cek/buat data kendaraan. 5. Cek transaksi aktif (status='masuk'). 6. Jika tidak ada transaksi aktif → buat entry (ASSIGN SLOT + increment terisi). 7. Jika ada transaksi aktif → proses exit (update waktu_keluar + decrement terisi). 8. Log aktivitas ke `tb_log_aktivitas`. 9. Buat notifikasi. 10. Dispatch event `ANPRDetected`. |
| **Output** | JSON: `{success, action, plate, vehicle, transaksi}` |

**Fungsi Pendukung**:
- `PlatNomorNormalizer::normalize()` - Menormalisasi format plat nomor
- `AreaParkir::findNextAvailableSlot()` - Mencari slot kosong
- `LogsActivity::logActivity()` - Mencatat log aktivitas

**Prosedur Pendukung**:
- `$area->increment('terisi')` - Menambah kapasitas terisi saat entry
- `$area->decrement('terisi')` - Mengurangi kapasitas terisi saat exit

---

#### 1.1.2 Method: `scan`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request` dengan file `image` (jpg/png, max 5MB) |
| **Proses** | 1. Upload gambar ke Cloudinary. 2. Gunakan `PlateRecognizerService` untuk deteksi plat. 3. Matching dengan database kendaraan. 4. Cek transaksi aktif untuk kendaraan tersebut. |
| **Output** | JSON: `{success, plate_number, confidence, vehicle, box, transaksi, image_url}` |

---

#### 1.1.3 Method: `applyCheckoutTotals`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Transaksi $transaksi`, `Carbon $waktuKeluar` |
| **Proses** | 1. Load tarif transaksi. 2. Hitung durasi (ceil menit ke jam). 3. Hitung `biaya_total = durasi_jam * tarif_perjam`. 4. Update fields: `waktu_keluar`, `durasi_jam`, `biaya_total`, `status='keluar'`. |
| **Output** | Void (update langsung ke model) |

---

### 1.2 RfidParkingController

**Lokasi**: `app/Http/Controllers/RfidParkingController.php`

#### 1.2.1 Method: `processScan`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request` berisi `rfid_uid` (string) |
| **Proses** | 1. Cari RFID tag di `rfid_tags` (metode baru) atau `tb_user.rfid_uid` (fallback). 2. Rate limiting (cooldown 2 detik). 3. Cek kendaraan terkait. 4. **Entry Flow**: Jika tidak ada transaksi aktif → buat transaksi + increment terisi. 5. **Exit Flow**: Jika ada transaksi aktif → hitung biaya (dengan diskon 10% untuk member RFID), proses pembayaran saldo. 6. Buat `RfidTransaction` record. |
| **Output** | JSON: `{success, message, user{...}, amount, payment_required{...}}` |

**Fungsi/Fitur Khusus**:
- Diskon 10% untuk user dengan `rfid_uid` (member)
- `lockForUpdate()` untuk mencegah race condition saat checkout
- `Cache::put()` untuk rate limiting

**Prosedur Pendukung**:
- `$area->increment('terisi')` - Saat check-in berhasil
- `$area->decrement('terisi')` - Saat check-out berhasil
- `Pembayaran::create()` - Mencatat pembayaran otomatis saat checkout

---

#### 1.2.2 Method: `resolveCheckInArea`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `User $currentUser` atau `null` |
| **Proses** | 1. Prioritas 1: Cek `id_area` di profil user. 2. Prioritas 2: Cek sesi `operational_area_id` dari kode peta. |
| **Output** | `AreaParkir` object atau `null` |

---

### 1.3 TransaksiController

**Lokasi**: `app/Http/Controllers/TransaksiController.php`

#### 1.3.1 Method: `index`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request` dengan filter opsional: `q` (search), `status`, `tanggal_dari`, `tanggal_sampai` |
| **Proses** | 1. Query `Transaksi` dengan relasi. 2. Apply filters jika ada. 3. Paginate 15 per halaman. |
| **Output** | View `transaksi.index` dengan `transaksis` |

---

#### 1.3.2 Method: `show`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `$id_parkir` (integer) |
| **Proses** | 1. Find transaksi dengan relasi (kendaraan, tarif, user, area, pembayaran). 2. Load data pembayaran terkait. |
| **Output** | View `transaksi.show` dengan `transaksi` |

---

#### 1.3.3 Method: `update`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request` berisi data update, `$id_parkir` |
| **Proses** | 1. Find transaksi. 2. Validasi hak akses (user harus owner atau admin/petugas/owner). 3. Update fields yang diizinkan. 4. Log aktivitas. |
| **Output** | Redirect dengan success/error message |

---

#### 1.3.4 Method: `destroy`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `$id_parkir` |
| **Proses** | 1. Validasi hak akses. 2. Pastikan transaksi bukan status 'masuk' (sedang parkir). 3. Soft delete transaksi. 4. Log aktivitas. |
| **Output** | Redirect dengan success message |

---

## Modul 2: Kendaraan

### 2.1 KendaraanController

**Lokasi**: `app/Http/Controllers/KendaraanController.php`

#### Method: `index`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request` dengan filter `q`, `jenis_kendaraan`, `id_area` |
| **Proses** | 1. Query kendaraan dengan relasi user & area. 2. Apply filters. 3. Paginate. |
| **Output** | View dengan data kendaraan |

---

#### Method: `store`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `plat_nomor`, `jenis_kendaraan`, `warna`, `pemilik`, `id_user` |
| **Proses** | 1. Validasi input. 2. Normalisasi plat nomor. 3. Cek duplikat. 4. Create record. 5. Log aktivitas. |
| **Output** | Redirect ke index dengan success message |

---

### 2.2 UserVehicleController

**Lokasi**: `app/Http/Controllers/UserVehicleController.php`

#### Method: `index`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | Authenticated user |
| **Proses** | Ambil kendaraan milik user yang login,urutkan berdasarkan plat_nomor |
| **Output** | View dengan daftar kendaraan user |

---

#### Method: `store`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `plat_nomor`, `jenis_kendaraan`, `warna`, `pemilik` |
| **Proses** | 1. Batasi maksimal 2 kendaraan per user. 2. Validasi. 3. Normalisasi plat. 4. Cek duplikat. 5. Create. |
| **Output** | Redirect dengan success/error message |

---

#### Method: `update`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request` dengan data baru, `Kendaraan $vehicle` |
| **Proses** | 1. Validasi ownership. 2. Cek kendaraan sedang parkir (jika ya, reject). 3. Jika ubah plat, cek RFID terhubung. 4. Update. |
| **Output** | Redirect dengan success/error |

---

#### Method: `destroy`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Kendaraan $vehicle` |
| **Proses** | 1. Validasi ownership. 2. Cek tidak sedang parkir. 3. Cek tidak ada RFID terhubung. 4. Delete. |
| **Output** | Redirect dengan success/error |

---

## Modul 3: Pembayaran

### 3.1 PaymentController

**Lokasi**: `app/Http/Controllers/PaymentController.php`

#### Method: `selectTransaction`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request` dengan `q` (search opsional) |
| **Proses** | Ambil transaksi dengan `status='keluar'` dan `status_pembayaran != 'berhasil'` |
| **Output** | View `payment.select-transaction` |

---

#### Method: `create`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `$id_parkir` |
| **Proses** | 1. Find transaksi. 2. Jika sudah lunas, redirect ke success. 3. Cek kas shift terbuka (untuk cash). 4. Load data terkait. |
| **Output** | View `payment.create` |

---

#### Method: `success`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `$id_parkir` |
| **Proses** | 1. Validasi hak akses. 2. Sync status Midtrans jika ada `midtrans_order_id`. 3. Refresh data. |
| **Output** | View `payment.success` |

---

#### Method: `midtransPay`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `$id_parkir` |
| **Proses** | 1. Validasi transaksi. 2. Siapkan data untuk Midtrans Snap. 3. Buat view dengan client key. |
| **Output** | View `payment.midtrans-pay` |

---

#### Method: `midtransSnapToken`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`, `$id_parkir` |
| **Proses** | 1. Validasi. 2. Konfigurasi Midtrans. 3. Generate Snap Token. |
| **Output** | JSON dengan `snap_token`, `redirect_url` |

---

#### Method: `midtransCallback`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request` dari Midtrans (notification) |
| **Proses** | 1. Handle notification. 2. Update status transaksi & pembayaran sesuai hasil. |
| **Output** | Response 200 ke Midtrans |

---

### 3.2 CashPaymentController

**Lokasi**: `app/Http/Controllers/CashPaymentController.php`

#### Method: `create`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `$id_parkir` |
| **Proses** | 1. Validasi transaksi. 2. Cek kas shift terbuka. 3. Hitung nominal. |
| **Output** | View form pembayaran cash |

---

#### Method: `store`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `id_parkir`, `jumlah_bayar`, `keterangan` |
| **Proses** | 1. Validasi. 2. Gunakan DB transaction. 3. Update transaksi + buat pembayaran. 4. Update kas shift. 5. Log aktivitas. |
| **Output** | Redirect dengan success message |

---

### 3.3 SaldoController

**Lokasi**: `app/Http/Controllers/SaldoController.php`

#### Method: `topup`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `jumlah` |
| **Proses** | 1. Validasi jumlah (min 10.000). 2. DB transaction. 3. Update user balance. 4. Buat `SaldoHistory`. |
| **Output** | Redirect dengan success/error |

---

#### Method: `history`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | Authenticated user |
| **Proses** | Ambil history saldo user dengan pagination |
| **Output** | View dengan history |

---

## Modul 4: Area Parkir & Slot

### 4.1 AreaParkirController

**Lokasi**: `app/Http/Controllers/AreaParkirController.php`

#### Method: `index`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request` dengan `q` (search) |
| **Proses** | Ambil semua area dengan relasi, support search |
| **Output** | View `area.index` |

---

#### Method: `store`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `nama_area`, `lokasi`, `kapasitas`, `map_code`, `is_default_map` |
| **Proses** | 1. Validasi. 2. Create. 3. Generate map code jika kosong. 4. Log aktivitas. |
| **Output** | Redirect dengan success |

---

#### Method: `update`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`, `$id` |
| **Proses** | 1. Validasi. 2. Update. 3. Log. |
| **Output** | Redirect dengan success |

---

#### Method: `destroy`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `$id` |
| **Proses** | 1. Cek tidak ada transaksi aktif. 2. Delete. 3. Log. |
| **Output** | Redirect dengan success/error |

---

### 4.2 ParkingSlotController

**Lokasi**: `app/Http/Controllers/ParkingSlotController.php`

#### Method: `manage`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `$areaId` |
| **Proses** | Ambil area dengan slot-slotnya |
| **Output** | View dengan area dan slot |

---

## Modul 5: User & Authentication

### 5.1 UserController

**Lokasi**: `app/Http/Controllers/UserController.php`

#### Method: `index`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request` dengan `q`, `role` filter |
| **Proses** | Query users dengan filters, paginate |
| **Output** | View `admin.users.index` |

---

#### Method: `store`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `name`, `email`, `password`, `role`, `id_area`, `rfid_uid` |
| **Proses** | 1. Validasi. 2. Create user. 3. Log. |
| **Output** | Redirect dengan success |

---

#### Method: `update`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`, `$id` |
| **Proses** | 1. Validasi. 2. Update fields. 3. Log. |
| **Output** | Redirect dengan success |

---

#### Method: `destroy`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `$id` |
| **Proses** | 1. Validasi hak akses. 2. Cek bukan user yang sedang login. 3. Delete. 4. Log. |
| **Output** | Redirect dengan success/error |

---

### 5.2 Auth Controllers

**LoginController**, **RegisterController**, dll.

| Method | Input | Proses | Output |
|--------|-------|--------|--------|
| `login` | Request(email, password) | Auth attempt, regenerate session | Redirect/JSON |
| `register` | Request(user data) | Create user, send verification | Redirect |
| `logout` | - | Auth logout, invalidate session | Redirect |
| `verify` | $id, $hash | Mark email as verified | Redirect |
| `sendResetLink` | Request(email) | Kirim reset password email | JSON/Redirect |

---

### 5.3 RFID Admin & Access Controllers

#### RfidAdminController

| Method | Deskripsi |
|--------|-----------|
| `index` | Daftar semua RFID tags |
| `create` | Form tambah RFID |
| `store` | Simpan RFID baru (generate UID atau manual) |
| `edit` | Form edit |
| `update` | Update RFID |
| `destroy` | Hapus RFID |

#### RfidAccessController

| Method | Input | Proses | Output |
|--------|-------|--------|--------|
| `login` | Request(uid) | Auth user via RFID | JSON dengan token |
| `logout` | Request(token) | Invalidate token | JSON |
| `me` | Request(token) | Get current user profile | JSON |

---

## Modul 6: Dashboard & Report

### 6.1 DashboardController

**Lokasi**: `app/Http/Controllers/DashboardController.php`

#### Method: `index`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | - |
| **Proses** | Ambil statistik: total kendaraan, transaksi hari ini, pendapatan hari ini, kapasitas total |
| **Output** | View `dashboard.admin` |

---

### 6.2 PetugasDashboardController

**Lokasi**: `app/Http/Controllers/PetugasDashboardController.php`

#### Method: `setOperationalArea`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `kode_peta` |
| **Proses** | 1. Validasi kode peta. 2. Simpan `operational_area_id` di session. |
| **Output** | Redirect/JSON dengan success |

---

#### Method: `index`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | Authenticated petugas |
| **Proses** | 1. Resolve area operasional (prioritas: id_area profil, lalu sesi). 2. Jika tidak ada, tampilkan form kode peta. 3. Ambil statistik: transaksi aktif, booking, transaksi hari ini, pendapatan. 4. Cache stats per area (60 detik). |
| **Output** | View `petugas.dashboard` |

---

### 6.3 OwnerDashboardController

**Lokasi**: `app/Http/Controllers/OwnerDashboardController.php`

#### Method: `index`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | Authenticated owner |
| **Proses** | Ambil statistik lengkap semua area: pendapatan per area, statistik kendaraan, dll |
| **Output** | View `owner.dashboard` |

---

### 6.4 ReportController

**Lokasi**: `app/Http/Controllers/ReportController.php`

#### Method: `index`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `tanggal_dari`, `tanggal_sampai`, `id_area` |
| **Proses** | Generate laporan transaksi berdasarkan filter |
| **Output** | View dengan data laporan |

---

### 6.5 RevenueReconciliationController

**Lokasi**: `app/Http/Controllers/RevenueReconciliationController.php`

#### Method: `index`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | - |
| **Proses** | 1. Hitung total dari `tb_transaksi` (where berhasil). 2. Hitung total dari `tb_pembayaran` (where berhasil). 3. Cari transaksi tanpa id_pembayaran. |
| **Output** | View `admin.reconciliation.revenue` |

---

#### Method: `syncMissingPayments`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | - |
| **Proses** | 1. Lock transaksi yang tidak punya pembayaran. 2. Buat `Pembayaran` untuk masing-masing. 3. Update `id_pembayaran` di transaksi. |
| **Output** | Redirect dengan jumlah yang disinkronkan |

---

## Modul 7: Konfigurasi & Utility

### 7.1 TarifController

**Lokasi**: `app/Http/Controllers/TarifController.php`

#### Method: `store`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `jenis_kendaraan`, `tarif_perjam` |
| **Proses** | 1. Validasi. 2. Create. 3. Log aktivitas. |
| **Output** | Redirect dengan success |

---

#### Method: `update`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`, `$id` |
| **Proses** | 1. Validasi. 2. Update. 3. Log dengan old & new data. |
| **Output** | Redirect dengan success |

---

#### Method: `destroy`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `$id` |
| **Proses** | 1. Cek belum dipakai di transaksi. 2. Delete. 3. Log. |
| **Output** | Redirect dengan success/error |

---

### 7.2 LogAktifitasController

**Lokasi**: `app/Http/Controllers/LogAktifitasController.php`

#### Method: `index`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `q`, `type`, `tanggal` |
| **Proses** | Ambil log dengan filters, paginate |
| **Output** | View `log-aktivitas.index` |

---

### 7.3 CameraController

**Lokasi**: `app/Http/Controllers/CameraController.php`

#### Method: `index`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `id_area` |
| **Proses** | Ambil kamera berdasarkan area |
| **Output** | View dengan daftar kamera |

---

#### Method: `store`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `nama`, `url`, `jenis` (entry/exit/lpr), `id_area` |
| **Proses** | Create kamera baru |
| **Output** | Redirect dengan success |

---

## Modul 8: API External

### 8.1 PlateRecognizerController (API)

**Lokasi**: `app/Http/Controllers/Api/PlateRecognizerController.php`

#### Method: `scanPlate`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `image` (file), `debug` (boolean opsional) |
| **Proses** | 1. Validasi gambar. 2. Panggil `PlateRecognizerService`. 3. Return hasil deteksi. |
| **Output** | JSON: `{success, plate_number, color, confidence, valid, message, raw_response}` |

---

### 8.2 Api\ANPRController

**Lokasi**: `app/Http/Controllers\Api\ANPRController.php`

#### Method: `handleDetection`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request` dari perangkat keras ANPR |
| **Proses** | Sama dengan `ANPRController::handleDetection()` |
| **Output** | JSON response |

---

### 8.3 Api\RfidParkingController

**Lokasi**: `app/Http/Controllers\Api\RfidParkingController.php`

#### Method: `processScan`

| Aspek | Deskripsi |
|-------|-----------|
| **Input** | `Request`: `rfid_uid` |
| **Proses** | Sama dengan `RfidParkingController::processScan()` |
| **Output** | JSON response |

---

## Lampiran: Model Relationship

```
User
  ├── hasMany Kendaraan
  ├── hasMany Transaksi
  ├── hasMany SaldoHistory
  └── belongsTo AreaParkir (id_area)

Kendaraan
  ├── belongsTo User
  ├── hasMany Transaksi
  └── hasOne RfidTag

Transaksi
  ├── belongsTo Kendaraan
  ├── belongsTo User
  ├── belongsTo AreaParkir
  ├── belongsTo Tarif
  ├── belongsTo Pembayaran (nullable)
  └── belongsTo ParkingMapSlot (nullable)

Pembayaran
  ├── belongsTo Transaksi
  └── belongsTo User (petugas yang memproses)

AreaParkir
  ├── hasMany Transaksi
  ├── hasMany Cameras
  └── hasMany ParkingMapSlots

Tarif
  └── hasMany Transaksi
```

---

*Dokumen ini dibuat untuk keperluan laporan proyek NESTON Smart Parking System*
