# NESTON Website Documentation

Dokumen ini merangkum sistem yang ada saat ini pada project `neston`: arsitektur, modul, fitur, alur kerja, file terkait, tabel data, konfigurasi `.env`, dan cuplikan kode inti.

---

## 1) Ringkasan Arsitektur

- **Framework:** Laravel 12 (PHP 8.2), Blade templating.
- **Frontend:** TailwindCSS, AlpineJS, Vite bundler.
- **Database:** MySQL (tabel domain memakai prefix `tb_` + beberapa tabel framework Laravel).
- **Queue:** Database queue (`QUEUE_CONNECTION=database`) untuk proses async (email/WhatsApp).
- **Integrasi eksternal:**
  - Midtrans (pembayaran online)
  - WhatsApp Gateway (Fonnte/UltraMsg style)
  - Plate Recognizer API (ANPR)
  - Cloudinary (media)

**Entry points utama:**
- Routing web: `routes/web.php`
- Middleware alias: `bootstrap/app.php`
- Provider aplikasi: `app/Providers/AppServiceProvider.php`

---

## 2) Daftar Sistem Utama

1. **Autentikasi + Verifikasi Email + Role-based Access**
2. **Manajemen User & Kendaraan**
3. **Operasional Parkir (Check-in / Check-out / Riwayat)**
4. **Peta Parkir, Slot, Booking & Reservasi**
5. **ANPR (Automatic Number Plate Recognition)**
6. **RFID (login, akses, parking operation)**
7. **Pembayaran (Midtrans, Saldo/NestonPay, Tunai)**
8. **Kas Shift (open/close shift kasir)**
9. **Notifikasi (Email + WhatsApp)**
10. **Dashboard & Reporting**
11. **Contact, Language, dan halaman pendukung**

---

## 3) Modul per Sistem

## 3.1 Auth, Verification, RBAC

**Fitur:**
- Login, register, logout
- Verifikasi email
- Lupa password/reset password
- Pembatasan akses berdasarkan role (`admin`, `petugas`, `user`, `owner`)

**File utama:**
- `app/Http/Controllers/Auth/LoginController.php`
- `app/Http/Controllers/Auth/RegisterController.php`
- `app/Http/Controllers/Auth/VerifyEmailController.php`
- `app/Http/Middleware/RoleMiddleware.php`
- `bootstrap/app.php`
- `routes/web.php`

**Tabel:**
- `tb_user`
- `password_reset_tokens`
- `sessions`

---

## 3.2 User, Profile, Vehicle, Wallet

**Fitur:**
- Profile management user
- Kelola kendaraan milik user
- Saldo NestonPay (topup manual / Midtrans token)
- Bayar tagihan dengan saldo

**File utama:**
- `app/Http/Controllers/UserController.php`
- `app/Http/Controllers/UserVehicleController.php`
- `app/Http/Controllers/SaldoController.php`
- `app/Models/User.php`
- `app/Models/Kendaraan.php`
- `app/Models/SaldoHistory.php`

**View utama:**
- `resources/views/user/dashboard.blade.php`
- `resources/views/user/profile.blade.php`
- `resources/views/user/bookings.blade.php`

**Tabel:**
- `tb_user`
- `tb_kendaraan`
- `tb_saldo_history`
- `tb_pembayaran`

---

## 3.3 Operasional Parkir (Core)

**Fitur:**
- Check-in kendaraan (manual + scanner opsional)
- Check-out kendaraan + hitung durasi/biaya
- Daftar parkir aktif, booking aktif, dan riwayat
- Cetak struk

**File utama:**
- `app/Http/Controllers/TransaksiController.php`
- `app/Models/Transaksi.php`
- `resources/views/parkir/create.blade.php`
- `resources/views/transaksi/index.blade.php`

**Tabel:**
- `tb_transaksi`
- `tb_tarif`
- `tb_area_parkir`
- `tb_kendaraan`
- `tb_parking_map_slots`

**Cuplikan kode inti (check-in):**
```php
// app/Http/Controllers/TransaksiController.php
$request->validate([
    'plat_nomor' => 'required|string|max:15',
    'id_tarif' => 'required|exists:tb_tarif,id_tarif',
    'id_area' => 'required|exists:tb_area_parkir,id_area',
]);
```

**Cuplikan kode inti (check-out):**
```php
// app/Http/Controllers/TransaksiController.php
$transaksi->update([
    'waktu_keluar' => $waktu_keluar,
    'durasi_jam' => $durasi_jam,
    'biaya_total' => $biaya_total,
    'status' => 'keluar',
    'status_pembayaran' => 'pending',
]);
```

---

## 3.4 Peta Parkir, Slot, Reservasi

**Fitur:**
- Visualisasi area dan slot parkir
- Pengikatan kamera ke area/map
- Booking slot user
- Reservasi slot aktif dengan expiry rule

**File utama:**
- `app/Http/Controllers/ParkingSlotController.php`
- `app/Http/Controllers/AreaParkirController.php`
- `app/Models/ParkingMapSlot.php`
- `app/Models/ParkingMapCamera.php`
- `app/Models/ParkingSlotReservation.php`
- `resources/views/parking-map.blade.php`

**Tabel:**
- `tb_area_parkir`
- `tb_parking_map_slots`
- `tb_parking_map_cameras`
- `tb_parking_slot_reservations`
- `tb_kamera`

---

## 3.5 ANPR

**Fitur:**
- Scan plat nomor dari kamera/upload
- Validasi hasil OCR + confidence
- Integrasi ke proses check-in/check-out
- Notifikasi deteksi terbaru

**File utama:**
- `app/Http/Controllers/ANPRController.php`
- `app/Http/Controllers/Api/PlateRecognizerController.php`
- `app/Services/PlateRecognizerService.php`
- `app/Models/ANPRScan.php`
- `resources/views/anpr/index.blade.php`
- `resources/views/components/plate-scanner.blade.php`

**Tabel:**
- `tb_anpr_scans`
- relasi ke `tb_transaksi` (nullable)

---

## 3.6 RFID

**Fitur:**
- RFID login
- RFID access control
- RFID scan untuk operasi parkir
- Manajemen tag RFID (admin)

**File utama:**
- `app/Http/Controllers/RfidParkingController.php`
- `app/Http/Controllers/RfidLoginController.php`
- `app/Http/Controllers/RfidAccessController.php`
- `app/Http/Controllers/RfidAdminController.php`
- `app/Models/RfidTag.php`
- `app/Models/RfidTransaction.php`
- `app/Models/ParkingLog.php`

**Tabel:**
- `rfid_tags`
- `tb_rfid_transactions`
- `parking_logs`

---

## 3.7 Pembayaran

**Fitur:**
- Midtrans payment flow
- NestonPay (saldo) payment
- Cash intent + cash confirm
- Payment status sync dan success page

**File utama:**
- `app/Http/Controllers/PaymentController.php`
- `app/Http/Controllers/SaldoController.php`
- `app/Http/Controllers/CashPaymentController.php`
- `app/Models/Pembayaran.php`
- `resources/views/payment/create.blade.php`

**Tabel:**
- `tb_pembayaran`
- `tb_transaksi`
- `tb_saldo_history`

**Cuplikan kode Midtrans callback route:**
```php
// routes/web.php
Route::post('/payment/midtrans/notification', [PaymentController::class, 'midtransNotification'])
    ->name('payment.midtrans.notification');
```

---

## 3.8 Kas Shift

**Fungsi utama:**
- Membuka sesi kas (shift) per petugas/area
- Mengunci perubahan cash payment setelah shift ditutup
- Menjaga audit trail operasional kasir

**File utama:**
- `app/Http/Controllers/CashPaymentController.php`
- `app/Models/KasShift.php`
- `database/migrations/2026_04_11_120000_add_cash_columns_and_kas_shift.php`

**Tabel:**
- `tb_kas_shift`
- kolom terkait di `tb_pembayaran`: `id_kas_shift`, `cash_received`, `cash_change`

**Cuplikan kode penting:**
```php
// app/Http/Controllers/CashPaymentController.php
if ($shift->isClosed()) {
    throw ValidationException::withMessages([
        'shift' => ['Shift sudah ditutup; pembayaran tunai tidak dapat dikonfirmasi.'],
    ]);
}
```

---

## 3.9 Notifikasi (Email + WhatsApp)

**Fitur:**
- Trigger otomatis saat check-in/check-out
- Kirim email ke user
- Kirim WhatsApp ke nomor user
- Simpan log notifikasi

**File utama:**
- `app/Observers/TransaksiObserver.php`
- `app/Events/ParkingCheckedIn.php`
- `app/Events/ParkingCheckedOut.php`
- `app/Listeners/SendParkingCheckInEmail.php`
- `app/Listeners/SendParkingCheckOutEmail.php`
- `app/Listeners/SendParkingCheckInWhatsApp.php`
- `app/Listeners/SendParkingCheckOutWhatsApp.php`
- `app/Services/WhatsAppGateway.php`
- `app/Models/NotificationLog.php`

**Tabel:**
- `notification_logs`

**Cuplikan kode observer:**
```php
// app/Observers/TransaksiObserver.php
if ($transaksi->status === 'masuk' && $transaksi->waktu_masuk && $transaksi->id_user) {
    event(new ParkingCheckedIn($transaksi));
}
```

---

## 3.10 Dashboard & Laporan

**Fitur:**
- Dashboard per role
- Rekap transaksi/pembayaran
- Export CSV laporan owner
- Revenue reconciliation admin

**File utama:**
- `app/Http/Controllers/DashboardController.php`
- `app/Http/Controllers/OwnerDashboardController.php`
- `app/Http/Controllers/PetugasDashboardController.php`
- `app/Http/Controllers/ReportController.php`
- `app/Http/Controllers/RevenueReconciliationController.php`

---

## 4) Routing Penting (Ringkas)

**Operasional:**
- `transaksi.create-check-in` (`GET /check-in`)
- `transaksi.checkIn` (`POST /check-in`)
- `transaksi.checkOut` (`PUT /transaksi/{id}/check-out`)

**ANPR:**
- `anpr.index` (`GET /anpr`)
- `api.anpr.scan` (`POST /api/anpr/scan`)
- `api.scan-plate` (`POST /scan-plate`)

**RFID:**
- `api.parkir.rfid-scan`
- `api.rfid.login`
- `api.rfid.identify`
- `api.rfid.access.scan`

**Payment:**
- `payment.create`
- `payment.midtrans.token`
- `payment.midtrans.notification`
- `kas.cash.intent`, `kas.cash.confirm`

---

## 5) Konfigurasi `.env` Penting

**Aplikasi & DB**
- `APP_ENV`, `APP_DEBUG`, `APP_URL`, `APP_TIMEZONE`
- `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

**Queue**
- `QUEUE_CONNECTION=database`

**Midtrans**
- `MIDTRANS_SERVER_KEY`
- `MIDTRANS_CLIENT_KEY`
- `MIDTRANS_IS_PRODUCTION`

**WhatsApp Gateway**
- `WHATSAPP_ENABLED`
- `WHATSAPP_DRIVER`
- `WHATSAPP_GATEWAY_URL`
- `WHATSAPP_API_TOKEN`

**ANPR**
- `PLATE_RECOGNIZER_KEY`
- `ANPR_API_TOKEN`

**Cloudinary**
- `CLOUDINARY_URL`
- `CLOUDINARY_CLOUD_NAME`
- `CLOUDINARY_KEY`
- `CLOUDINARY_SECRET`

---

## 6) Struktur Folder yang Paling Sering Dipakai

- `app/Http/Controllers` → logic request/response per fitur
- `app/Models` → entity Eloquent + relasi
- `app/Services` → service integrasi (contoh: WhatsApp, ANPR)
- `app/Listeners`, `app/Events`, `app/Observers` → event-driven process
- `resources/views` → Blade UI
- `routes` → route web/api/console
- `database/migrations` → struktur DB
- `docs` → dokumentasi internal proyek

---

## 7) Daftar Tabel Domain Inti

- `tb_user`
- `tb_kendaraan`
- `tb_tarif`
- `tb_area_parkir`
- `tb_transaksi`
- `tb_pembayaran`
- `tb_kamera`
- `tb_parking_map_slots`
- `tb_parking_map_cameras`
- `tb_parking_slot_reservations`
- `tb_anpr_scans`
- `tb_rfid_transactions`
- `rfid_tags`
- `parking_logs`
- `tb_saldo_history`
- `tb_kas_shift`
- `notification_logs`
- `tb_log_aktivitas`
- `tb_messages`

---

## 8) Catatan Maintenance / Technical Notes

- Event notifikasi berjalan via queue; pastikan worker aktif:
  - `php artisan queue:work`
- Setelah ubah config `.env`, jalankan:
  - `php artisan config:clear`
  - `php artisan cache:clear`
- Dokumentasi ini menggambarkan kondisi terkini kode pada saat file ini dibuat.

---

## 9) Referensi Dokumen Tambahan

- ERD simbolik (XML draw.io):
  - `docs/erd-symbolic.xml`

---

## 10) Penjelasan Entry Points (Kode Ini Untuk Apa?)

Bagian ini menjelaskan alur "masuk ke aplikasi" dari request pertama sampai logic bisnis dieksekusi.

### 10.1 `public/index.php`
- **Fungsi:** pintu masuk semua request HTTP.
- **Peran:** bootstrap framework Laravel dan meneruskan request ke kernel aplikasi.
- **Kapan dipakai:** setiap kali user mengakses halaman web/API.

### 10.2 `bootstrap/app.php`
- **Fungsi:** konfigurasi inti aplikasi (routing + middleware alias).
- **Peran:**
  - mendaftarkan file route (`routes/web.php`, `routes/console.php`)
  - menambahkan alias middleware seperti `role`, `no-cache`, `verified`.
- **Dampak:** menentukan request harus melewati aturan apa sebelum masuk controller.

### 10.3 `routes/web.php`
- **Fungsi:** peta URL ke controller method.
- **Peran:** mendefinisikan endpoint, nama route, middleware, dan grouping berdasarkan role.
- **Contoh:**
  - `GET /check-in` → form check-in
  - `POST /check-in` → proses check-in
  - `PUT /transaksi/{id}/check-out` → proses check-out

### 10.4 Controller (mis. `TransaksiController`)
- **Fungsi:** orchestration request → validasi → proses data → response.
- **Peran:** tempat business logic utama per fitur.
- **Contoh check-in:**
  1) validasi input  
  2) cek kapasitas area  
  3) cek kendaraan/transaksi aktif  
  4) simpan `tb_transaksi`  
  5) kirim feedback UI

### 10.5 Model Eloquent (mis. `Transaksi`, `Kendaraan`)
- **Fungsi:** representasi tabel database.
- **Peran:** query data, relasi antartabel, helper domain.
- **Contoh:** `Transaksi::where(...)->lockForUpdate()->first()`

### 10.6 Observer + Event + Listener
- **Fungsi:** menjalankan aksi lanjutan tanpa menumpuk logic di controller.
- **Alur pada project ini:**
  - `TransaksiObserver` mendeteksi perubahan status masuk/keluar
  - dispatch `ParkingCheckedIn` / `ParkingCheckedOut`
  - listener kirim email/WhatsApp async via queue

### 10.7 View Blade + Alpine
- **Fungsi:** presentasi UI dan interaksi frontend.
- **Peran:** menampilkan form, tombol, scanner, status loading/error/success.
- **Contoh:** `resources/views/components/plate-scanner.blade.php` untuk UI ANPR scanner reusable.

---

## 11) Code Reading Guide (Kalau Penguji Tanya "Ini Kode Buat Apa?")

Gunakan format jawaban berikut saat menjelaskan potongan kode:

1. **Context**: kode ini berada di layer apa? (route/controller/model/service/view)
2. **Goal**: kode ini menyelesaikan masalah apa?
3. **Input**: data apa yang masuk?
4. **Process**: validasi/komputasi/query apa yang dilakukan?
5. **Output/Side effect**: update DB? kirim event? balikan response?

Contoh praktis:

### 11.1 Validasi request check-in
```php
$request->validate([
    'plat_nomor' => 'required|string|max:15',
    'id_tarif' => 'required|exists:tb_tarif,id_tarif',
    'id_area' => 'required|exists:tb_area_parkir,id_area',
]);
```
- **Untuk apa:** mencegah data transaksi parkir tidak valid masuk ke sistem.
- **Dampak:** request langsung ditolak jika field wajib kosong/tidak cocok.

### 11.2 Update status check-out
```php
$transaksi->update([
    'waktu_keluar' => $waktu_keluar,
    'durasi_jam' => $durasi_jam,
    'biaya_total' => $biaya_total,
    'status' => 'keluar',
    'status_pembayaran' => 'pending',
]);
```
- **Untuk apa:** menutup sesi parkir dan menyiapkan fase pembayaran.
- **Dampak:** data transaksi masuk ke tahap "menunggu pembayaran".

### 11.3 Trigger event notifikasi
```php
if ($transaksi->status === 'masuk' && $transaksi->waktu_masuk && $transaksi->id_user) {
    event(new ParkingCheckedIn($transaksi));
}
```
- **Untuk apa:** memisahkan proses notifikasi dari logic transaksi utama.
- **Dampak:** email/WhatsApp bisa diproses asynchronous lewat queue.

---

## 12) Sequence Alur Kode per Fitur (Teknis)

### 12.1 Sequence Check-in
1. `routes/web.php` memetakan `POST /check-in` ke `TransaksiController@checkIn`
2. Controller validasi input (`plat_nomor`, `id_tarif`, `id_area`)
3. Controller lock area/kendaraan/transaksi aktif (anti race condition)
4. Simpan transaksi baru status `masuk`
5. `TransaksiObserver@created` memicu event check-in
6. Listener notifikasi antre di queue
7. UI menerima redirect + flash message sukses/gagal

### 12.2 Sequence Check-out + Pembayaran
1. `PUT /transaksi/{id}/check-out` ke `TransaksiController@checkOut`
2. Hitung durasi + total biaya + diskon (jika ada aturan)
3. Update transaksi status `keluar`, payment `pending`
4. User/petugas lanjut ke modul pembayaran
5. Jika cash: lewat `CashPaymentController` (intent → confirm)
6. Jika berhasil: `status_pembayaran=berhasil` dan relasi ke `tb_pembayaran`
7. Observer update memicu event checkout notification

### 12.3 Sequence Notifikasi WhatsApp
1. Event diterima listener `SendParkingCheckInWhatsApp` / `SendParkingCheckOutWhatsApp`
2. Listener menyiapkan message template
3. `WhatsAppGateway::sendToUser()` validasi nomor + config
4. Gateway call API provider
5. Hasil dicatat ke `notification_logs`

---

## 13) Peta File Berdasarkan Layer

### 13.1 Layer Routing
- `routes/web.php`
- `routes/console.php`

### 13.2 Layer HTTP/Middleware
- `app/Http/Controllers/**`
- `app/Http/Middleware/**`

### 13.3 Layer Domain/Data
- `app/Models/**`
- `database/migrations/**`

### 13.4 Layer Integrasi
- `app/Services/PlateRecognizerService.php`
- `app/Services/WhatsAppGateway.php`

### 13.5 Layer Eventing
- `app/Observers/TransaksiObserver.php`
- `app/Events/*`
- `app/Listeners/*`

### 13.6 Layer Presentation
- `resources/views/**`
- komponen reusable: `resources/views/components/**`

---

## 14) Narasi Teknis untuk Menjawab Penguji

Jika penguji menunjuk satu file dan bertanya "ini fungsinya apa?", jawaban ideal:

- **`routes/web.php`**: "Ini katalog endpoint. Dari sini kita tahu URL mana memanggil method mana, dan role apa yang boleh akses."
- **`TransaksiController.php`**: "Ini pusat logic operasional parkir, termasuk validasi check-in/check-out, lock transaksi, dan update status."
- **`CashPaymentController.php`**: "Ini khusus alur tunai dan kas shift, jadi cash tidak bercampur logic Midtrans/saldo."
- **`TransaksiObserver.php`**: "Ini hook otomatis saat transaksi dibuat/diupdate, dipakai untuk trigger event notifikasi tanpa menambah beban controller."
- **`WhatsAppGateway.php`**: "Ini adapter integrasi provider WA, termasuk normalisasi nomor, call API, dan logging hasil kirim."
- **`resources/views/components/plate-scanner.blade.php`**: "Komponen UI scanner reusable untuk menangkap plat dari kamera dan mengisi input target."

---

## 15) Batasan Dokumen

- Dokumen ini fokus pada **fungsi kode dan alur runtime**, bukan listing semua baris kode.
- Untuk detail endpoint contract (request/response body), buat dokumen lanjutan `api-documentation.md`.
- Untuk detail schema kolom per tabel, gunakan migration + ERD (`docs/erd-symbolic.xml`).

