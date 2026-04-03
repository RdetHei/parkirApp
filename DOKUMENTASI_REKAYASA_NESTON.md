# Dokumentasi Rekayasa Proyek NESTON
## Periode: 15 Januari 2026 – 13 Februari 2026

Dokumentasi ini disusun untuk keperluan presentasi dan memuat ringkasan perkembangan teknis proyek sistem parkir NESTON, dilengkapi penjelasan detail kode untuk setiap fitur.

---

## Daftar Isi
1. [Ringkasan Proyek](#1-ringkasan-proyek)
2. [Timeline Pengembangan](#2-timeline-pengembangan)
3. [Fase 1: Fondasi Sistem (14–15 Januari)](#3-fase-1-fondasi-sistem-14-15-januari)
4. [Fase 2: Sistem Pembayaran (18–19 Januari)](#4-fase-2-sistem-pembayaran-18-19-januari)
5. [Fase 3: Peningkatan Arsitektur (22 Januari)](#5-fase-3-peningkatan-arsitektur-22-januari)
6. [Fase 4: Integrasi Midtrans & Fitur Lanjutan (29 Januari – 11 Februari)](#6-fase-4-integrasi-midtrans--fitur-lanjutan-29-januari--11-februari)
7. [Fase 5: Plate Recognizer & Peta Parkir (Februari 2026)](#7-fase-5-plate-recognizer--peta-parkir-februari-2026)
8. [Penjelasan Kode Utama](#8-penjelasan-kode-utama)

---

## 1. Ringkasan Proyek

**NESTON** adalah sistem manajemen parkir berbasis web yang dibangun dengan Laravel 12. Fitur utama meliputi:

- **Check-in/Check-out** kendaraan dengan manajemen kapasitas area parkir
- **Pembayaran** manual, QR scan, dan online via Midtrans
- **Role-Based Access Control** (Admin, Petugas, Owner, User)
- **Scan plat nomor** otomatis dengan Plate Recognizer API
- **Peta parkir** dengan fitur bookmark slot
- **Laporan** transaksi dan pembayaran
- **Log aktivitas** untuk audit

---

## 2. Timeline Pengembangan

| Tanggal | Fase | Fitur/Perubahan |
|---------|------|-----------------|
| 14 Jan 2026 | Fondasi | Tabel User, Kendaraan, Area, Tarif, Transaksi, Log |
| 15 Jan 2026 | RBAC | Kolom `role` pada User |
| 18 Jan 2026 | Pembayaran | Tabel Pembayaran, relasi ke Transaksi |
| 19 Jan 2026 | Refactor | Cleanup tabel pembayaran |
| 22 Jan 2026 | Arsitektur | Soft Deletes, kolom Catatan |
| 29 Jan 2026 | Midtrans & UX | Kolom Midtrans, Bookmark slot, Kendaraan nullable |
| 11 Feb 2026 | Midtrans | `midtrans_order_id` pada Transaksi |
| Feb 2026 | Integrasi | Plate Recognizer, Peta Parkir |

---

## 3. Fase 1: Fondasi Sistem (14–15 Januari)

### 3.1 Struktur Database Awal

**File:** `database/migrations/2026_01_14_011328_create_tb_user_table.php`

```php
Schema::create('tb_user', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
```

**Penjelasan untuk presentasi:**
- Tabel `tb_user` menyimpan data pengguna sistem.
- `email` bersifat `unique` untuk mencegah duplikasi akun.
- `rememberToken` dipakai Laravel untuk fitur “Ingat Saya”.
- `timestamps` mencatat `created_at` dan `updated_at` otomatis.

---

### 3.2 Role-Based Access Control (RBAC)

**File:** `database/migrations/2026_01_15_000001_add_role_to_tb_user.php`

```php
Schema::table('tb_user', function (Blueprint $table) {
    $table->string('role')->default('user')->after('password');
});
```

**Penjelasan untuk presentasi:**
- Kolom `role` menjadi dasar RBAC.
- Nilai default `'user'` untuk pengguna baru.
- Role yang digunakan: `admin`, `petugas`, `owner`, `user`.

**Implementasi Middleware:**

**File:** `app/Http/Middleware/RoleMiddleware.php`

```php
public function handle(Request $request, Closure $next, string ...$roles)
{
    if (! Auth::check()) {
        return redirect()->route('login.create');
    }

    $user = Auth::user();
    $allowedRoles = array_map(fn($r) => strtolower(trim($r)), $roles);
    $userRole = strtolower(trim($user->role ?? ''));

    if (!in_array($userRole, $allowedRoles)) {
        abort(403, 'Unauthorized - Insufficient permissions');
    }

    return $next($request);
}
```

**Penjelasan untuk presentasi:**
- Middleware memastikan user sudah login.
- Mengecek apakah `role` user ada dalam daftar role yang diizinkan.
- Jika tidak, mengembalikan HTTP 403.
- Penggunaan: `->middleware(['role:admin,petugas'])`.

---

## 4. Fase 2: Sistem Pembayaran (18–19 Januari)

### 4.1 Tabel Pembayaran

**File:** `database/migrations/2026_01_18_123704_create_tb_pembayaran_table.php`

```php
Schema::create('tb_pembayaran', function (Blueprint $table) {
    $table->id('id_pembayaran');
    $table->unsignedBigInteger('id_parkir');
    $table->decimal('nominal', 10, 0);
    $table->enum('metode', ['manual', 'qr_scan'])->default('manual');
    $table->enum('status', ['pending', 'berhasil', 'gagal'])->default('pending');
    $table->text('keterangan')->nullable();
    $table->unsignedBigInteger('id_user')->nullable();
    $table->dateTime('waktu_pembayaran')->nullable();
    $table->timestamps();

    $table->foreign('id_parkir')->references('id_parkir')->on('tb_transaksi')->onDelete('cascade');
    $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('set null');
});
```

**Penjelasan untuk presentasi:**
- `id_parkir` menghubungkan pembayaran ke transaksi parkir.
- `nominal` menyimpan jumlah pembayaran.
- `metode`: manual atau qr_scan (kemudian ditambah midtrans).
- `status`: pending, berhasil, gagal.
- `id_user` opsional untuk mencatat petugas yang memproses.

---

### 4.2 Relasi Transaksi–Pembayaran

**File:** `database/migrations/2026_01_18_124124_add_pembayaran_to_tb_transaksi_table.php`

```php
$table->enum('status_pembayaran', ['pending', 'berhasil', 'gagal'])->default('pending');
$table->unsignedBigInteger('id_pembayaran')->nullable();
$table->foreign('id_pembayaran')->references('id_pembayaran')->on('tb_pembayaran')->onDelete('set null');
```

**Penjelasan untuk presentasi:**
- `status_pembayaran` di transaksi memudahkan filter transaksi belum/sudah dibayar.
- `id_pembayaran` menyimpan referensi ke record pembayaran.
- `onDelete('set null')` agar transaksi tetap ada jika pembayaran dihapus.

---

## 5. Fase 3: Peningkatan Arsitektur (22 Januari)

### 5.1 Soft Deletes

**File:** `database/migrations/2026_01_22_000001_add_soft_delete_to_tables.php`

```php
if (Schema::hasTable('tb_user') && !Schema::hasColumn('tb_user', 'deleted_at')) {
    Schema::table('tb_user', function (Blueprint $table) {
        $table->softDeletes();
    });
}
// Sama untuk tb_kendaraan, tb_transaksi, tb_pembayaran
```

**Penjelasan untuk presentasi:**
- Soft delete menambah kolom `deleted_at`.
- Data tidak dihapus fisik, hanya ditandai.
- Berguna untuk audit, recovery, dan compliance.
- Di Model: `use SoftDeletes;`.

---

### 5.2 Kolom Catatan Transaksi

**File:** `database/migrations/2026_01_22_000002_add_catatan_to_tb_transaksi_table.php`

Kolom `catatan` memungkinkan petugas menambah informasi tambahan per transaksi (misalnya kondisi kendaraan, karcis manual, dll.).

---

### 5.3 TransaksiObserver untuk Log Aktivitas

**File:** `app/Observers/TransaksiObserver.php`

```php
public function created(Transaksi $transaksi): void
{
    if (Auth::check()) {
        LogAktifitas::create([
            'id_user' => Auth::id(),
            'aktivitas' => 'Membuat transaksi parkir #' . str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT),
            'waktu_aktivitas' => Carbon::now(),
        ]);
    }
}

public function updated(Transaksi $transaksi): void
{
    if ($transaksi->isDirty('status') && $transaksi->status === 'keluar') {
        $activity = 'Mencatat kendaraan keluar parkir #' . str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT);
    }
    // ... LogAktifitas::create
}
```

**Penjelasan untuk presentasi:**
- Observer otomatis dipanggil saat transaksi dibuat/diupdate/dihapus.
- Setiap aksi dicatat di `tb_log_aktivitas`.
- Berguna untuk audit dan pelacakan siapa melakukan apa.

---

## 6. Fase 4: Integrasi Midtrans & Fitur Lanjutan (29 Januari – 11 Februari)

### 6.1 Kolom Midtrans di Pembayaran

**File:** `database/migrations/2026_01_29_100000_add_midtrans_fields_to_tb_pembayaran.php`

```php
$table->string('order_id', 64)->nullable()->after('id_parkir');
$table->string('transaction_id', 64)->nullable()->after('order_id');
$table->string('payment_type', 32)->nullable()->after('transaction_id');

// Ubah enum ke VARCHAR agar bisa: manual, qr_scan, midtrans
DB::statement('ALTER TABLE tb_pembayaran MODIFY metode VARCHAR(50)');
DB::statement('ALTER TABLE tb_pembayaran MODIFY status VARCHAR(50)');
```

**Penjelasan untuk presentasi:**
- `order_id`: ID order di Midtrans.
- `transaction_id`: ID transaksi pembayaran Midtrans.
- `payment_type`: tipe pembayaran (bank transfer, e-wallet, dll.).
- Metode dan status diubah ke VARCHAR agar bisa menampung nilai dari Midtrans.

---

### 6.2 Midtrans Order ID di Transaksi

**File:** `database/migrations/2026_02_11_000001_add_midtrans_order_id_to_tb_transaksi.php`

```php
$table->string('midtrans_order_id', 100)->nullable()->after('id_pembayaran');
```

**Penjelasan untuk presentasi:**
- Menyimpan `order_id` Midtrans di transaksi.
- Digunakan untuk sinkronisasi status jika webhook tidak sampai (misalnya di localhost).
- Saat user membuka halaman success, sistem bisa cek status ke API Midtrans.

---

### 6.3 Fitur Bookmark Slot Parkir

**File:** `database/migrations/2026_01_29_023655_add_bookmarked_status_to_transaksis_table.php`

```php
$table->enum('status', ['masuk', 'keluar', 'bookmarked'])->change();
$table->dateTime('bookmarked_at')->nullable()->after('status');
```

**Penjelasan untuk presentasi:**
- Status `bookmarked` untuk slot yang dipesan sementara.
- `bookmarked_at` untuk timer (misalnya 10 menit).
- Slot yang dibookmark tidak bisa dipakai orang lain sampai waktu habis.

---

### 6.4 Kendaraan Fleksibel (Nullable)

**File:** `database/migrations/2026_01_29_000001_make_tb_kendaraan_fields_nullable.php`

Kolom `id_user`, `warna`, `pemilik` di `tb_kendaraan` diubah menjadi nullable agar check-in tetap bisa dilakukan meskipun data kendaraan belum lengkap.

---

### 6.5 Alur Pembayaran Midtrans di Controller

**File:** `app/Http/Controllers/PaymentController.php`

**a) Generate Snap Token**

```php
$order_id = 'PARKIR-' . $id_parkir . '-' . time();
$transaksi->update(['midtrans_order_id' => $order_id]);

$params = [
    'transaction_details' => [
        'order_id' => $order_id,
        'gross_amount' => $gross_amount,
    ],
    'item_details' => [...],
    'customer_details' => [...],
    'callbacks' => ['finish' => $finishUrl, 'unfinish' => $unfinishUrl, 'error' => $errorUrl],
];

$snapToken = \Midtrans\Snap::getSnapToken($params);
```

**Penjelasan untuk presentasi:**
- `order_id` unik per transaksi.
- `midtrans_order_id` disimpan untuk sinkronisasi nanti.
- Snap Token dipakai untuk menampilkan halaman pembayaran Midtrans.

**b) Verifikasi Notifikasi**

```php
// Verifikasi dengan API Midtrans, bukan hanya dari body POST
$statusResponse = \Midtrans\Transaction::status($order_id);
$transaction_status = $statusResponse->transaction_status;

if (in_array($transaction_status, ['capture', 'settlement'])) {
    $this->applyMidtransSuccess($id_parkir, ...);
}
```

**Penjelasan untuk presentasi:**
- Status pembayaran dicek langsung ke API Midtrans.
- Mencegah manipulasi notifikasi palsu.
- Hanya `capture` dan `settlement` yang dianggap berhasil.

**c) Sinkronisasi Status (Fallback)**

```php
private function syncMidtransPaymentStatus(int $id_parkir): bool
{
    // Jika status_pembayaran belum 'berhasil' dan ada midtrans_order_id
    // Panggil API Midtrans untuk cek status
    // Jika settlement/capture → applyMidtransSuccess
}
```

**Penjelasan untuk presentasi:**
- Dipakai saat user membuka halaman success setelah bayar.
- Berguna jika webhook Midtrans tidak sampai (misalnya di localhost).
- Memastikan pembayaran tetap tercatat.

---

## 7. Fase 5: Plate Recognizer & Peta Parkir (Februari 2026)

### 7.1 Plate Recognizer Service

**File:** `app/Services/PlateRecognizerService.php`

```php
public function scanPlate($image, bool $includeRawResponse = false): array
{
    $response = Http::timeout(30)
        ->withHeaders(['Authorization' => 'Token ' . $this->apiKey])
        ->attach('upload', file_get_contents($image->getRealPath()), $image->getClientOriginalName())
        ->post($this->apiUrl);

    if ($response->failed()) {
        throw new \Exception("Plate Recognizer API error: ...");
    }

    $data = $response->json();
    if (empty($data['results'])) {
        return ['plate_number' => null, 'confidence' => 0, 'valid' => false, ...];
    }

    $firstResult = $data['results'][0];
    $plateNumber = $firstResult['plate'] ?? null;
    $confidence = floatval($firstResult['score'] ?? 0);
    $isValid = $confidence >= 0.80; // Threshold 80%

    return [
        'plate_number' => $plateNumber,
        'confidence' => $confidence,
        'valid' => $isValid,
        'message' => $isValid ? 'Plat nomor berhasil dideteksi' : 'Plat tidak valid (confidence di bawah 80%)',
    ];
}
```

**Penjelasan untuk presentasi:**
- Menggunakan Laravel HTTP Client.
- API key disimpan di `.env`, tidak di frontend.
- Threshold 80% untuk validasi hasil.
- Error handling untuk kegagalan API dan hasil kosong.

---

### 7.2 Plate Recognizer Controller

**File:** `app/Http/Controllers/Api/PlateRecognizerController.php`

```php
public function scanPlate(Request $request): JsonResponse
{
    $request->validate([
        'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // 5MB
    ]);

    $image = $request->file('image');
    $result = $this->plateRecognizerService->scanPlate($image, $request->boolean('debug'));

    return response()->json([
        'success' => true,
        'plate_number' => $result['plate_number'],
        'confidence' => $result['confidence'],
        'valid' => $result['valid'],
        'message' => $result['message'],
    ]);
}
```

**Penjelasan untuk presentasi:**
- Validasi file: max 5MB, format JPG/PNG.
- Controller hanya menerima request dan memanggil service.
- Logic bisnis ada di service (separation of concerns).

---

### 7.3 Komponen Kamera Frontend

**File:** `resources/views/components/plate-scanner.blade.php`

Fitur utama:
- `getUserMedia` dengan `facingMode: 'environment'` (kamera belakang).
- Tombol Buka Kamera, Ambil Foto, Scan Plat, Ambil Ulang.
- Upload via `fetch()` ke `/scan-plate`.
- Auto-fill select kendaraan jika plat terdeteksi dan valid.
- Loading indicator dan pesan error/sukses.

---

### 7.4 Peta Parkir (ParkingMapController)

**File:** `app/Http/Controllers/Api/ParkingMapController.php`

```php
$parkingAreas = AreaParkir::with(['transaksis' => function($query) {
    $query->where(function($q) {
        $q->whereNull('waktu_keluar')->where('status', 'masuk');
    })->orWhere(function($q) {
        $q->where('status', 'bookmarked')
          ->where('bookmarked_at', '>', Carbon::now()->subMinutes(10));
    });
}, 'transaksis.kendaraan', 'transaksis.user'])->get();

$mapData = $parkingAreas->map(function($area) {
    $status = 'empty';
    if ($occupiedTransaction) $status = 'occupied';
    elseif ($bookmarkedTransaction) $status = 'bookmarked';
    return ['id' => $area->id_area, 'name' => $area->nama_area, 'status' => $status, ...];
});
```

**Penjelasan untuk presentasi:**
- Mengambil area parkir beserta transaksi aktif dan bookmark.
- Status slot: `empty`, `occupied`, `bookmarked`.
- Bookmark berlaku 10 menit.
- Data dikembalikan sebagai JSON untuk frontend.

---

## 8. Penjelasan Kode Utama

### 8.1 Check-in dengan Database Transaction & Lock

**File:** `app/Http/Controllers/TransaksiController.php`

```php
public function checkIn(Request $request)
{
    $transaksi = DB::transaction(function () use ($request) {
        // Lock area untuk mencegah race condition
        $area = AreaParkir::lockForUpdate()->findOrFail($request->id_area);

        if ($area->terisi >= $area->kapasitas) {
            throw new \Exception('Kapasitas area parkir sudah penuh');
        }

        $transaksi = Transaksi::create([...]);
        $area->increment('terisi');

        return $transaksi;
    });
}
```

**Penjelasan untuk presentasi:**
- `DB::transaction()` memastikan semua operasi berhasil atau di-rollback.
- `lockForUpdate()` mencegah dua petugas memakai slot yang sama bersamaan.
- `increment('terisi')` mengupdate kapasitas secara atomik.

---

### 8.2 Check-out dengan Perhitungan Biaya

```php
$waktu_keluar = Carbon::now();
$durasi_jam = ceil($waktu_keluar->diffInMinutes($transaksi->waktu_masuk) / 60);
$biaya_total = $durasi_jam * $transaksi->tarif->tarif_perjam;

$transaksi->update([
    'waktu_keluar' => $waktu_keluar,
    'durasi_jam' => $durasi_jam,
    'biaya_total' => $biaya_total,
    'status' => 'keluar',
    'status_pembayaran' => 'pending',
]);

$area->decrement('terisi');
```

**Penjelasan untuk presentasi:**
- Durasi dihitung dalam jam (dibulatkan ke atas).
- Biaya = durasi × tarif per jam.
- Status diubah ke `keluar`, pembayaran ke `pending`.
- Kapasitas area dikurangi.

---

### 8.3 Model Transaksi dengan Accessor

**File:** `app/Models/Transaksi.php`

```php
public function getBiayaTotalAttribute()
{
    if ($this->attributes['biaya_total'] ?? null) {
        return $this->attributes['biaya_total'];
    }
    if ($this->waktu_masuk && $this->waktu_keluar && $this->tarif) {
        $durasi = $this->getDurasiJamAttribute();
        return $durasi * $this->tarif->tarif_perjam;
    }
    return 0;
}
```

**Penjelasan untuk presentasi:**
- Accessor memungkinkan `$transaksi->biaya_total` dihitung otomatis jika belum disimpan.
- Berguna untuk preview biaya sebelum checkout.

---

## Ringkasan untuk Presentasi

1. **Fondasi (14–15 Jan):** Database, RBAC, middleware role.
2. **Pembayaran (18–19 Jan):** Tabel pembayaran, relasi ke transaksi.
3. **Arsitektur (22 Jan):** Soft deletes, catatan, observer log aktivitas.
4. **Midtrans (29 Jan – 11 Feb):** Kolom Midtrans, webhook, sinkronisasi status.
5. **UX & Integrasi (Feb):** Bookmark slot, kendaraan nullable, Plate Recognizer, peta parkir.

**Poin penting:**
- Database transaction dan row lock untuk konsistensi data.
- Verifikasi pembayaran via API Midtrans, bukan hanya dari webhook.
- Service layer untuk Plate Recognizer (modular, mudah diuji).
- RBAC untuk keamanan akses.
- Soft deletes untuk audit dan recovery.

---

## Lampiran A: Diagram Entity-Relationship (ERD)

```
User (1) ----< Kendaraan (M)     [id_user nullable]
User (1) ----< Transaksi (M)    [id_user = operator]
User (1) ----< Pembayaran (M)   [id_user = petugas]
User (1) ----< LogAktifitas (M)

Kendaraan (1) ----< Transaksi (M)
AreaParkir (1) ----< Transaksi (M)
Tarif (1) ----< Transaksi (M)

Transaksi (1) ---- Pembayaran (1)  [id_pembayaran, status_pembayaran]
```

**Tabel utama:**
- `tb_user` - Pengguna (admin, petugas, owner, user)
- `tb_kendaraan` - Data kendaraan (plat_nomor, jenis, warna, pemilik)
- `tb_area_parkir` - Area parkir (nama, kapasitas, terisi)
- `tb_tarif` - Tarif per jam per jenis kendaraan
- `tb_transaksi` - Transaksi parkir (check-in/check-out)
- `tb_pembayaran` - Record pembayaran (manual, qr_scan, midtrans)
- `tb_log_aktivitas` - Audit trail aktivitas user

---

## Lampiran B: Alur Check-in & Check-out (Activity Diagram)

**Check-in:**
1. Petugas buka form "Catat Kendaraan Masuk"
2. Pilih kendaraan, tarif, area (bisa pakai scan plat)
3. Sistem validasi → DB Transaction + Lock Area
4. Cek kapasitas (terisi < kapasitas)
5. Buat Transaksi, increment terisi
6. Commit → Redirect ke Parkir Aktif

**Check-out:**
1. Petugas pilih transaksi dari Parkir Aktif
2. Konfirmasi checkout
3. DB Transaction + Lock Transaksi
4. Hitung durasi & biaya
5. Update Transaksi (status keluar), decrement terisi
6. Redirect ke Pilih Transaksi Pembayaran

**Pembayaran:**
- Manual: Petugas konfirmasi → Buat Pembayaran → Update Transaksi
- QR: Customer scan → Signed URL → Buat Pembayaran → Update Transaksi
- Midtrans: Customer bayar online → Webhook/API → Buat Pembayaran → Update Transaksi

---

## Lampiran C: Lokasi File Penting

| Kategori | File |
|----------|------|
| **Migrations** | `database/migrations/` |
| **Models** | `app/Models/` |
| **Controllers** | `app/Http/Controllers/` |
| **Services** | `app/Services/PlateRecognizerService.php` |
| **Middleware** | `app/Http/Middleware/RoleMiddleware.php` |
| **Observers** | `app/Observers/TransaksiObserver.php` |
| **Routes** | `routes/web.php` |
| **Views** | `resources/views/` |
| **Components** | `resources/views/components/plate-scanner.blade.php` |
| **Config** | `config/services.php`, `config/midtrans.php` |

---

## Lampiran D: Tips Presentasi

1. **Mulai dengan overview:** Tunjukkan ERD dan alur bisnis check-in → check-out → pembayaran.
2. **Demo live:** Check-in dengan scan plat → Check-out → Pembayaran Midtrans.
3. **Soroti keamanan:** RBAC, verifikasi Midtrans via API, API key di backend.
4. **Soroti konsistensi data:** DB transaction, row lock, soft delete.
5. **Jelaskan arsitektur:** Service layer, observer, separation of concerns.
6. **Siapkan jawaban:** "Bagaimana jika webhook Midtrans tidak sampai?" → Sinkronisasi saat buka halaman success.

---

*Dokumentasi ini dibuat untuk mendukung presentasi proyek NESTON.*

# Dokumentasi Rekayasa Lengkap Proyek NESTON
## Sistem Manajemen Parkir Berbasis Web
### Periode: 14 Januari 2026 – 23 Februari 2026

---

## Daftar Isi

1. [Ringkasan Proyek](#1-ringkasan-proyek)
2. [Teknologi & Stack](#2-teknologi--stack)
3. [Timeline Pengembangan Lengkap](#3-timeline-pengembangan-lengkap)
4. [Fase 1: Inisialisasi & Fondasi Database (14 Januari 2026)](#4-fase-1-inisialisasi--fondasi-database-14-januari-2026)
5. [Fase 2: Role-Based Access Control (15 Januari 2026)](#5-fase-2-role-based-access-control-15-januari-2026)
6. [Fase 3: Sistem Pembayaran (18-19 Januari 2026)](#6-fase-3-sistem-pembayaran-18-19-januari-2026)
7. [Fase 4: Peningkatan Arsitektur (22 Januari 2026)](#7-fase-4-peningkatan-arsitektur-22-januari-2026)
8. [Fase 5: Integrasi Midtrans & Fitur Lanjutan (29 Januari - 11 Februari 2026)](#8-fase-5-integrasi-midtrans--fitur-lanjutan-29-januari---11-februari-2026)
9. [Fase 6: Plate Recognizer & Peta Parkir (Februari 2026)](#9-fase-6-plate-recognizer--peta-parkir-februari-2026)
10. [Fase 7: Optimasi Form Check-in & Simplifikasi Pembayaran (23 Februari 2026)](#10-fase-7-optimasi-form-check-in--simplifikasi-pembayaran-23-februari-2026)
11. [Arsitektur Sistem](#11-arsitektur-sistem)
12. [Penjelasan Kode Utama dengan Logika](#12-penjelasan-kode-utama-dengan-logika)
13. [Diagram Alur Bisnis](#13-diagram-alur-bisnis)

---

## 1. Ringkasan Proyek

**NESTON** adalah sistem manajemen parkir berbasis web yang dibangun dengan **Laravel 12** dan **Tailwind CSS**. Sistem ini dirancang untuk mengelola operasional parkir secara digital dengan fitur-fitur modern dan efisien.

### Fitur Utama:
- ✅ **Check-in/Check-out** kendaraan dengan manajemen kapasitas real-time
- ✅ **Pembayaran online** via Midtrans (GoPay, OVO, DANA, Transfer Bank, Kartu Kredit)
- ✅ **Scan plat nomor otomatis** dengan Plate Recognizer API
- ✅ **Form check-in fleksibel** - mendukung kendaraan terdaftar dan kendaraan baru
- ✅ **Role-Based Access Control** (Admin, Petugas, Owner, User)
- ✅ **Peta parkir interaktif** dengan fitur bookmark slot
- ✅ **Laporan transaksi & pembayaran** dengan export CSV
- ✅ **Log aktivitas** untuk audit trail
- ✅ **Dashboard** dengan statistik real-time

---

## 2. Teknologi & Stack

### Backend:
- **Framework:** Laravel 12 (PHP 8.2+)
- **Database:** MySQL/MariaDB
- **ORM:** Eloquent
- **Payment Gateway:** Midtrans PHP SDK
- **API Integration:** Plate Recognizer API

### Frontend:
- **CSS Framework:** Tailwind CSS 4.1.18
- **JavaScript:** Alpine.js 3.x (via CDN)
- **Build Tool:** Vite 7.0.7
- **Icons:** SVG inline

### Tools & Dependencies:
- **Composer:** Dependency management
- **NPM:** Frontend dependencies
- **Git:** Version control

---

## 3. Timeline Pengembangan Lengkap

| Tanggal | Commit | Fase | Fitur/Perubahan |
|---------|--------|------|-----------------|
| 14 Jan 2026 | Initial commit | **Fondasi** | Setup Laravel 12, struktur database dasar |
| 14 Jan 2026 | first push | **Database** | Tabel User, Kendaraan, Area, Tarif, Transaksi, Log |
| 15 Jan 2026 | third push | **RBAC** | Kolom `role` pada User, RoleMiddleware |
| 18 Jan 2026 | third push | **Pembayaran** | Tabel Pembayaran, relasi ke Transaksi |
| 19 Jan 2026 | forth push | **Refactor** | Cleanup tabel pembayaran duplikat |
| 22 Jan 2026 | sixth/seventh push | **Arsitektur** | Soft Deletes, kolom Catatan, TransaksiObserver |
| 28 Jan 2026 | nineth push | **UI/UX** | Improve form designs across all features |
| 29 Jan 2026 | tenth push | **Midtrans** | Kolom Midtrans di Pembayaran, Bookmark slot |
| 29 Jan 2026 | - | **Fleksibilitas** | Kendaraan fields nullable |
| 11 Feb 2026 | sixteenth push | **Midtrans** | `midtrans_order_id` pada Transaksi |
| Feb 2026 | - | **Integrasi** | Plate Recognizer API, Peta Parkir |
| 22 Feb 2026 | - | **Cleanup** | Hapus pembayaran manual & QR, hanya Midtrans |
| 23 Feb 2026 | - | **Optimasi** | Form check-in baru dengan dual mode |

---

## 4. Fase 1: Inisialisasi & Fondasi Database (14 Januari 2026)

### 4.1 Setup Proyek Laravel

**Commit:** `ad3fc9e` - Initial commit

Proyek dimulai dengan Laravel 12. Struktur dasar dibuat dengan:
- `composer.json` dengan Laravel 12
- `package.json` dengan Tailwind CSS 4 dan Vite
- Konfigurasi database MySQL

### 4.2 Tabel User (`tb_user`)

**File:** `database/migrations/2026_01_14_011328_create_tb_user_table.php`

```php
Schema::create('tb_user', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    $table->timestamps();
});
```

**Penjelasan:**
- Tabel dasar untuk autentikasi pengguna
- `email` unique untuk mencegah duplikasi
- `rememberToken` untuk fitur "Ingat Saya"
- `timestamps` untuk audit (created_at, updated_at)

**Model:** `app/Models/User.php`
- Menggunakan Laravel's built-in authentication
- Relationship: `hasMany` Kendaraan, Transaksi, Pembayaran, LogAktifitas

---

### 4.3 Tabel Kendaraan (`tb_kendaraan`)

**File:** `database/migrations/2026_01_14_011330_create_tb_kendaraan_table.php`

```php
Schema::create('tb_kendaraan', function (Blueprint $table) {
    $table->id('id_kendaraan');
    $table->string('plat_nomor', 15);
    $table->string('jenis_kendaraan', 20);
    $table->string('warna', 20);
    $table->string('pemilik', 100);
    $table->unsignedBigInteger('id_user');
    $table->timestamps();
    
    $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('cascade');
});
```

**Penjelasan:**
- `plat_nomor` unique (dijamin di aplikasi, bukan di DB constraint)
- `id_user` nullable (diubah di migration 29 Jan) untuk kendaraan tamu
- Foreign key ke `tb_user` dengan cascade delete

**Model:** `app/Models/Kendaraan.php`
```php
protected $fillable = ['plat_nomor', 'jenis_kendaraan', 'warna', 'pemilik', 'id_user'];

public function transaksis() {
    return $this->hasMany(Transaksi::class, 'id_kendaraan', 'id_kendaraan');
}
```

---

### 4.4 Tabel Area Parkir (`tb_area_parkir`)

**File:** `database/migrations/2026_01_14_011329_create_tb_area_parkir_table.php`

```php
Schema::create('tb_area_parkir', function (Blueprint $table) {
    $table->id('id_area');
    $table->string('nama_area', 50);
    $table->integer('kapasitas');
    $table->integer('terisi')->default(0);
    $table->timestamps();
});
```

**Penjelasan:**
- `kapasitas`: total slot parkir
- `terisi`: jumlah slot yang sedang digunakan (real-time)
- Tidak ada soft delete karena area parkir tidak dihapus

**Logika Manajemen Kapasitas:**
- Saat check-in: `terisi++` (dengan lock untuk prevent race condition)
- Saat check-out: `terisi--` (dengan validasi > 0)

---

### 4.5 Tabel Tarif (`tb_tarif`)

**File:** `database/migrations/2026_01_14_011329_create_tb_tarif_table.php`

```php
Schema::create('tb_tarif', function (Blueprint $table) {
    $table->id('id_tarif');
    $table->enum('jenis_kendaraan', ['motor', 'mobil', 'lainnya']);
    $table->decimal('tarif_perjam', 10, 0);
    $table->timestamps();
});
```

**Penjelasan:**
- Tarif per jam berdasarkan jenis kendaraan
- Enum: motor, mobil, lainnya
- `tarif_perjam` dalam decimal untuk presisi

**Perhitungan Biaya:**
```php
$durasi_jam = ceil($waktu_keluar->diffInMinutes($waktu_masuk) / 60);
$biaya_total = $durasi_jam * $tarif->tarif_perjam;
```
- Durasi dibulatkan ke atas (ceil) - minimal 1 jam
- Contoh: 1 jam 5 menit = 2 jam

---

### 4.6 Tabel Transaksi (`tb_transaksi`)

**File:** `database/migrations/2026_01_14_011334_create_tb_transaksi_table.php`

```php
Schema::create('tb_transaksi', function (Blueprint $table) {
    $table->id('id_parkir');
    $table->unsignedBigInteger('id_kendaraan');
    $table->dateTime('waktu_masuk');
    $table->dateTime('waktu_keluar')->nullable();
    $table->unsignedBigInteger('id_tarif');
    $table->integer('durasi_jam')->nullable();
    $table->decimal('biaya_total', 10, 0)->nullable();
    $table->enum('status', ['masuk', 'keluar']);
    $table->unsignedBigInteger('id_user');
    $table->unsignedBigInteger('id_area');
    $table->timestamps();
    
    // Foreign Keys
    $table->foreign('id_kendaraan')->references('id_kendaraan')->on('tb_kendaraan')->onDelete('cascade');
    $table->foreign('id_tarif')->references('id_tarif')->on('tb_tarif')->onDelete('cascade');
    $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('cascade');
    $table->foreign('id_area')->references('id_area')->on('tb_area_parkir')->onDelete('cascade');
});
```

**Penjelasan:**
- `id_parkir` sebagai primary key (bukan `id`)
- `waktu_keluar`, `durasi_jam`, `biaya_total` nullable (diisi saat checkout)
- `status`: 'masuk' (aktif) atau 'keluar' (selesai)
- `id_user`: operator yang melakukan check-in
- Foreign keys dengan cascade delete untuk integritas data

**Model dengan Accessor:**
```php
public function getBiayaTotalAttribute() {
    if ($this->attributes['biaya_total'] ?? null) {
        return $this->attributes['biaya_total'];
    }
    // Auto-calculate jika belum ada
    if ($this->waktu_masuk && $this->waktu_keluar && $this->tarif) {
        $durasi = $this->getDurasiJamAttribute();
        return $durasi * $this->tarif->tarif_perjam;
    }
    return 0;
}
```

---

### 4.7 Tabel Log Aktivitas (`tb_log_aktivitas`)

**File:** `database/migrations/2026_01_14_011330_create_tb_log_aktivitas_table.php`

```php
Schema::create('tb_log_aktivitas', function (Blueprint $table) {
    $table->id('id_log');
    $table->unsignedBigInteger('id_user');
    $table->text('aktivitas');
    $table->dateTime('waktu_aktivitas');
    $table->timestamps();
    
    $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('cascade');
});
```

**Penjelasan:**
- Audit trail untuk semua aktivitas penting
- Mencatat siapa, apa, kapan
- Digunakan untuk compliance dan troubleshooting

---

## 5. Fase 2: Role-Based Access Control (15 Januari 2026)

### 5.1 Kolom Role pada User

**File:** `database/migrations/2026_01_15_000001_add_role_to_tb_user.php`

```php
Schema::table('tb_user', function (Blueprint $table) {
    $table->string('role')->default('user')->after('password');
});
```

**Role yang digunakan:**
- `admin`: Full access (CRUD semua data, akses log)
- `petugas`: Operasional (check-in/out, pembayaran)
- `owner`: Laporan & analitik
- `user`: Default (terbatas)

### 5.2 RoleMiddleware

**File:** `app/Http/Middleware/RoleMiddleware.php`

```php
public function handle(Request $request, Closure $next, string ...$roles)
{
    if (! Auth::check()) {
        return redirect()->route('login.create');
    }

    $user = Auth::user();
    $allowedRoles = array_map(fn($r) => strtolower(trim($r)), $roles);
    $userRole = strtolower(trim($user->role ?? ''));

    if (!in_array($userRole, $allowedRoles)) {
        abort(403, 'Unauthorized - Insufficient permissions');
    }

    return $next($request);
}
```

**Penjelasan:**
- Middleware memvalidasi role user sebelum akses route
- Support multiple roles: `->middleware(['role:admin,petugas'])`
- Case-insensitive untuk fleksibilitas
- Return 403 jika tidak authorized

**Registrasi Middleware:** `bootstrap/app.php`
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

---

## 6. Fase 3: Sistem Pembayaran (18-19 Januari 2026)

### 6.1 Tabel Pembayaran

**File:** `database/migrations/2026_01_18_123704_create_tb_pembayaran_table.php`

```php
Schema::create('tb_pembayaran', function (Blueprint $table) {
    $table->id('id_pembayaran');
    $table->unsignedBigInteger('id_parkir');
    $table->decimal('nominal', 10, 0);
    $table->enum('metode', ['manual', 'qr_scan'])->default('manual');
    $table->enum('status', ['pending', 'berhasil', 'gagal'])->default('pending');
    $table->text('keterangan')->nullable();
    $table->unsignedBigInteger('id_user')->nullable();
    $table->dateTime('waktu_pembayaran')->nullable();
    $table->timestamps();
    
    $table->foreign('id_parkir')->references('id_parkir')->on('tb_transaksi')->onDelete('cascade');
    $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('set null');
});
```

**Penjelasan:**
- `id_parkir`: relasi ke transaksi (one-to-one)
- `nominal`: jumlah pembayaran (bisa berbeda dari biaya_total untuk diskon)
- `metode`: manual, qr_scan (kemudian ditambah midtrans)
- `status`: pending, berhasil, gagal
- `id_user`: petugas yang memproses (nullable untuk pembayaran otomatis)

### 6.2 Relasi Pembayaran ke Transaksi

**File:** `database/migrations/2026_01_18_124124_add_pembayaran_to_tb_transaksi_table.php`

```php
Schema::table('tb_transaksi', function (Blueprint $table) {
    $table->enum('status_pembayaran', ['pending', 'berhasil', 'gagal'])->default('pending');
    $table->unsignedBigInteger('id_pembayaran')->nullable();
    $table->foreign('id_pembayaran')->references('id_pembayaran')->on('tb_pembayaran')->onDelete('set null');
});
```

**Penjelasan:**
- `status_pembayaran` di transaksi untuk quick filter
- `id_pembayaran` untuk relasi langsung
- `onDelete('set null')` agar transaksi tetap ada jika pembayaran dihapus

### 6.3 Cleanup Tabel Duplikat

**File:** `database/migrations/2026_01_19_000001_cleanup_pembayarans_table.php`

Migration ini menghapus tabel `pembayarans` yang duplikat (plural) dan memastikan hanya `tb_pembayaran` yang digunakan.

---

## 7. Fase 4: Peningkatan Arsitektur (22 Januari 2026)

### 7.1 Soft Deletes

**File:** `database/migrations/2026_01_22_000001_add_soft_delete_to_tables.php`

```php
// Add soft delete to tb_user
if (Schema::hasTable('tb_user') && !Schema::hasColumn('tb_user', 'deleted_at')) {
    Schema::table('tb_user', function (Blueprint $table) {
        $table->softDeletes();
    });
}
// Sama untuk tb_kendaraan, tb_transaksi, tb_pembayaran
```

**Penjelasan:**
- Data tidak dihapus fisik, hanya ditandai `deleted_at`
- Berguna untuk audit, recovery, compliance
- Di Model: `use SoftDeletes;`

**Query dengan Soft Delete:**
```php
Kendaraan::all(); // Hanya yang tidak dihapus
Kendaraan::withTrashed()->get(); // Termasuk yang dihapus
Kendaraan::onlyTrashed()->get(); // Hanya yang dihapus
```

### 7.2 Kolom Catatan

**File:** `database/migrations/2026_01_22_000002_add_catatan_to_tb_transaksi_table.php`

Kolom `catatan` memungkinkan petugas menambahkan informasi tambahan per transaksi (misalnya: kondisi kendaraan, karcis manual, dll).

### 7.3 TransaksiObserver

**File:** `app/Observers/TransaksiObserver.php`

```php
class TransaksiObserver
{
    public function created(Transaksi $transaksi): void
    {
        if (Auth::check()) {
            LogAktifitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Membuat transaksi parkir #' . str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT),
                'waktu_aktivitas' => Carbon::now(),
            ]);
        }
    }

    public function updated(Transaksi $transaksi): void
    {
        if (Auth::check()) {
            $activity = 'Mengupdate transaksi parkir #' . str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT);
            
            if ($transaksi->isDirty('status') && $transaksi->status === 'keluar') {
                $activity = 'Mencatat kendaraan keluar parkir #' . str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT);
            }
            
            LogAktifitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => $activity,
                'waktu_aktivitas' => Carbon::now(),
            ]);
        }
    }
}
```

**Registrasi Observer:** `app/Providers/AppServiceProvider.php`
```php
public function boot(): void
{
    Transaksi::observe(TransaksiObserver::class);
}
```

**Penjelasan:**
- Observer pattern untuk auto-logging
- Setiap create/update/delete transaksi otomatis tercatat
- Menggunakan `isDirty()` untuk deteksi perubahan spesifik

---

## 8. Fase 5: Integrasi Midtrans & Fitur Lanjutan (29 Januari - 11 Februari 2026)

### 8.1 Kolom Midtrans di Pembayaran

**File:** `database/migrations/2026_01_29_100000_add_midtrans_fields_to_tb_pembayaran.php`

```php
Schema::table('tb_pembayaran', function (Blueprint $table) {
    $table->string('order_id', 64)->nullable()->after('id_parkir');
    $table->string('transaction_id', 64)->nullable()->after('order_id');
    $table->string('payment_type', 32)->nullable()->after('transaction_id');
});

// Ubah enum ke VARCHAR agar bisa: manual, qr_scan, midtrans, dll
DB::statement('ALTER TABLE tb_pembayaran MODIFY metode VARCHAR(50) DEFAULT \'manual\'');
DB::statement('ALTER TABLE tb_pembayaran MODIFY status VARCHAR(50) DEFAULT \'pending\'');
```

**Penjelasan:**
- `order_id`: ID order di Midtrans (format: PARKIR-{id_parkir}-{timestamp})
- `transaction_id`: ID transaksi pembayaran dari Midtrans
- `payment_type`: bank_transfer, e_wallet, credit_card, dll
- Metode & status diubah ke VARCHAR untuk fleksibilitas

### 8.2 Midtrans Order ID di Transaksi

**File:** `database/migrations/2026_02_11_000001_add_midtrans_order_id_to_tb_transaksi.php`

```php
Schema::table('tb_transaksi', function (Blueprint $table) {
    $table->string('midtrans_order_id', 100)->nullable()->after('id_pembayaran');
});
```

**Penjelasan:**
- Menyimpan `order_id` Midtrans di transaksi untuk sinkronisasi
- Digunakan saat webhook tidak sampai (misalnya localhost)
- Saat user buka halaman success, sistem cek status ke API Midtrans

### 8.3 Alur Pembayaran Midtrans

**File:** `app/Http/Controllers/PaymentController.php`

#### a) Generate Snap Token

```php
public function midtransSnapToken(Request $request, $id_parkir)
{
    $transaksi = Transaksi::with(['kendaraan', 'tarif'])->findOrFail($id_parkir);
    
    // Validasi
    if ($transaksi->status_pembayaran === 'berhasil') {
        return response()->json(['error' => 'Transaksi sudah dibayar'], 400);
    }
    
    $serverKey = config('services.midtrans.server_key');
    $isProduction = config('services.midtrans.is_production');
    
    \Midtrans\Config::$serverKey = $serverKey;
    \Midtrans\Config::$isProduction = $isProduction;
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;
    
    // Generate unique order_id
    $order_id = 'PARKIR-' . $id_parkir . '-' . time();
    $gross_amount = (int) round((float) $transaksi->biaya_total);
    
    // Simpan order_id untuk sinkronisasi
    $transaksi->update(['midtrans_order_id' => $order_id]);
    
    $params = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => $gross_amount,
        ],
        'item_details' => [[
            'id' => (string) $id_parkir,
            'price' => $gross_amount,
            'quantity' => 1,
            'name' => 'Parkir - ' . ($transaksi->kendaraan->plat_nomor ?? 'Kendaraan'),
            'category' => 'Parkir',
        ]],
        'customer_details' => [
            'first_name' => $transaksi->kendaraan->pemilik ?? $transaksi->kendaraan->plat_nomor ?? 'Customer',
            'email' => $transaksi->user?->email ?? 'customer@parked.local',
        ],
        'callbacks' => [
            'finish' => route('payment.midtrans.finish', $id_parkir),
            'unfinish' => route('payment.midtrans.unfinish', $id_parkir),
            'error' => route('payment.midtrans.error', $id_parkir),
        ],
    ];
    
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    
    return response()->json([
        'snap_token' => $snapToken,
        'order_id' => $order_id,
    ]);
}
```

**Penjelasan:**
- Snap Token digunakan untuk menampilkan halaman pembayaran Midtrans
- `order_id` format: `PARKIR-{id_parkir}-{timestamp}` untuk identifikasi
- Callback URLs untuk redirect setelah pembayaran

#### b) Webhook Notifikasi (Idempotent)

```php
public function midtransNotification(Request $request)
{
    $payload = $request->all();
    $order_id = $payload['order_id'] ?? null;
    
    // Validasi format order_id
    if (!$order_id || !preg_match('/^PARKIR-(\d+)-/', $order_id, $m)) {
        return response()->json(['message' => 'Invalid order_id'], 400);
    }
    
    $id_parkir = (int) $m[1];
    
    // Verifikasi dengan API Midtrans (bukan hanya dari body POST)
    $serverKey = config('services.midtrans.server_key');
    \Midtrans\Config::$serverKey = $serverKey;
    \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
    
    $statusResponse = \Midtrans\Transaction::status($order_id);
    
    $transaction_status = strtolower($statusResponse->transaction_status ?? '');
    $successStatuses = ['capture', 'settlement'];
    
    if (!in_array($transaction_status, $successStatuses)) {
        return response()->json(['received' => true]);
    }
    
    // Apply pembayaran (idempotent)
    $this->applyMidtransSuccess($id_parkir, $order_id, ...);
    
    return response()->json(['received' => true]);
}
```

**Penjelasan:**
- **Idempotent**: Bisa dipanggil berkali-kali tanpa duplikasi
- **Verifikasi via API**: Status dicek langsung ke Midtrans, bukan hanya dari webhook body
- **Security**: Mencegah manipulasi webhook palsu
- Hanya `capture` dan `settlement` yang dianggap berhasil

#### c) Apply Midtrans Success (Idempotent)

```php
private function applyMidtransSuccess(int $id_parkir, string $order_id, ?string $transaction_id, ?string $payment_type, float $gross_amount): void
{
    DB::transaction(function () use ($id_parkir, $order_id, $transaction_id, $payment_type, $gross_amount) {
        $transaksi = Transaksi::lockForUpdate()->find($id_parkir);
        
        // Idempotent check
        if (!$transaksi || $transaksi->status_pembayaran === 'berhasil') {
            return; // Sudah diproses, skip
        }
        
        if ($transaksi->status !== 'keluar' || is_null($transaksi->biaya_total)) {
            return; // Invalid state
        }
        
        // Buat record pembayaran
        $pembayaran = Pembayaran::create([
            'id_parkir' => $id_parkir,
            'order_id' => $order_id,
            'transaction_id' => $transaction_id,
            'payment_type' => $payment_type,
            'nominal' => $gross_amount,
            'metode' => 'midtrans',
            'status' => 'berhasil',
            'keterangan' => 'Pembayaran Midtrans (' . ($payment_type ?? 'online') . ')',
            'id_user' => null, // Otomatis, bukan petugas
            'waktu_pembayaran' => Carbon::now(),
        ]);
        
        // Update transaksi
        $transaksi->update([
            'status_pembayaran' => 'berhasil',
            'id_pembayaran' => $pembayaran->id_pembayaran,
        ]);
    });
}
```

**Penjelasan:**
- **Row Lock**: `lockForUpdate()` mencegah double-payment
- **Idempotent**: Cek `status_pembayaran === 'berhasil'` sebelum proses
- **Transaction**: Semua operasi dalam DB transaction (atomic)

#### d) Sinkronisasi Status (Fallback)

```php
private function syncMidtransPaymentStatus(int $id_parkir): bool
{
    $transaksi = Transaksi::find($id_parkir);
    if (!$transaksi || $transaksi->status_pembayaran === 'berhasil') {
        return false;
    }
    
    $order_id = $transaksi->midtrans_order_id;
    if (empty($order_id)) {
        return false;
    }
    
    // Panggil API Midtrans untuk cek status
    $statusResponse = \Midtrans\Transaction::status($order_id);
    $transaction_status = strtolower($statusResponse->transaction_status ?? '');
    
    if (in_array($transaction_status, ['capture', 'settlement'])) {
        $this->applyMidtransSuccess($id_parkir, $order_id, ...);
        return true;
    }
    
    return false;
}
```

**Penjelasan:**
- Dipanggil saat user buka halaman success setelah bayar
- Berguna jika webhook tidak sampai (misalnya localhost)
- Memastikan pembayaran tetap tercatat meskipun webhook gagal

### 8.4 Fitur Bookmark Slot Parkir

**File:** `database/migrations/2026_01_29_023655_add_bookmarked_status_to_transaksis_table.php`

```php
Schema::table('tb_transaksi', function (Blueprint $table) {
    $table->enum('status', ['masuk', 'keluar', 'bookmarked'])->change();
    $table->dateTime('bookmarked_at')->nullable()->after('status');
});
```

**Penjelasan:**
- Status `bookmarked` untuk slot yang dipesan sementara
- `bookmarked_at` untuk timer (expire setelah 10 menit)
- Slot yang dibookmark tidak bisa dipakai orang lain

**API Bookmark:** `app/Http/Controllers/Api/ParkingMapController.php`
```php
public function bookmark(Request $request, $area_id)
{
    // Cek apakah area benar-benar kosong
    $existingActiveTransaction = Transaksi::where('id_area', $area_id)
        ->where(function($query) {
            $query->whereNull('waktu_keluar')
                  ->where('status', 'masuk');
        })->orWhere(function($query) {
            $query->where('status', 'bookmarked')
                  ->where('bookmarked_at', '>', Carbon::now()->subMinutes(10));
        })
        ->first();
    
    if ($existingActiveTransaction) {
        return response()->json(['message' => 'Slot parkir ini sedang terisi atau sudah dibookmark.'], 409);
    }
    
    // Buat transaksi bookmark
    $transaksi = Transaksi::create([
        'id_kendaraan' => null,
        'waktu_masuk' => null,
        'id_tarif' => null,
        'status' => 'bookmarked',
        'id_user' => Auth::id(),
        'id_area' => $area_id,
        'bookmarked_at' => Carbon::now(),
    ]);
    
    return response()->json(['message' => 'Slot berhasil dibookmark.', 'transaksi' => $transaksi], 200);
}
```

### 8.5 Kendaraan Fields Nullable

**File:** `database/migrations/2026_01_29_000001_make_tb_kendaraan_fields_nullable.php`

Kolom `id_user`, `warna`, `pemilik` diubah menjadi nullable agar check-in tetap bisa dilakukan meskipun data kendaraan belum lengkap (untuk kendaraan tamu).

---

## 9. Fase 6: Plate Recognizer & Peta Parkir (Februari 2026)

### 9.1 Plate Recognizer Service

**File:** `app/Services/PlateRecognizerService.php`

```php
class PlateRecognizerService
{
    private string $apiUrl = 'https://api.platerecognizer.com/v1/plate-reader/';
    private string $apiKey;
    private float $confidenceThreshold = 0.80; // 80%
    
    public function scanPlate($image, bool $includeRawResponse = false): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Plate Recognizer API key tidak dikonfigurasi.');
        }
        
        $response = Http::timeout(30)
            ->withHeaders(['Authorization' => 'Token ' . $this->apiKey])
            ->attach('upload', file_get_contents($image->getRealPath()), $image->getClientOriginalName())
            ->post($this->apiUrl);
        
        if ($response->failed()) {
            $errorMessage = $response->json('detail') ?? $response->json('message') ?? 'API request failed';
            throw new \Exception("Plate Recognizer API error: {$errorMessage}");
        }
        
        $data = $response->json();
        
        if (empty($data['results'])) {
            return [
                'plate_number' => null,
                'confidence' => 0,
                'valid' => false,
                'message' => 'Tidak ada plat nomor yang terdeteksi dalam gambar',
            ];
        }
        
        $firstResult = $data['results'][0];
        $plateNumber = $firstResult['plate'] ?? null;
        $confidence = floatval($firstResult['score'] ?? 0);
        $isValid = $confidence >= $this->confidenceThreshold;
        
        return [
            'plate_number' => $plateNumber,
            'confidence' => $confidence,
            'valid' => $isValid,
            'message' => $isValid 
                ? 'Plat nomor berhasil dideteksi' 
                : 'Plat tidak valid (confidence di bawah 80%)',
        ];
    }
}
```

**Penjelasan:**
- **Service Layer Pattern**: Logic API terpisah dari Controller
- **Error Handling**: Try-catch dengan logging
- **Confidence Threshold**: 80% minimum untuk validasi
- **Laravel HTTP Client**: Menggunakan `Http::` facade untuk API calls

### 9.2 Plate Recognizer Controller

**File:** `app/Http/Controllers/Api/PlateRecognizerController.php`

```php
public function scanPlate(Request $request): JsonResponse
{
    $request->validate([
        'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // 5MB
    ]);
    
    $image = $request->file('image');
    $result = $this->plateRecognizerService->scanPlate($image, $request->boolean('debug'));
    
    return response()->json([
        'success' => true,
        'plate_number' => $result['plate_number'],
        'confidence' => $result['confidence'],
        'valid' => $result['valid'],
        'message' => $result['message'],
    ]);
}
```

**Penjelasan:**
- Controller hanya handle request/response
- Validasi file: max 5MB, format JPG/PNG
- Logic bisnis di Service (separation of concerns)

### 9.3 Komponen Kamera Frontend

**File:** `resources/views/components/plate-scanner.blade.php`

**Fitur:**
- `getUserMedia` dengan `facingMode: 'environment'` (kamera belakang)
- Capture, preview, upload via `fetch()`
- Auto-fill input target setelah scan
- Loading indicator & error handling

**JavaScript Logic:**
```javascript
async scanPlate() {
    // Convert data URL to blob
    const response = await fetch(this.capturedImage);
    const blob = await response.blob();
    
    // Create FormData
    const formData = new FormData();
    formData.append('image', blob, 'plate-image.jpg');
    
    // Send to backend
    const scanResponse = await fetch('{{ route("api.scan-plate") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData
    });
    
    const data = await scanResponse.json();
    
    if (data.valid && data.plate_number) {
        this.fillTargetInput(data.plate_number);
    }
}
```

### 9.4 Peta Parkir API

**File:** `app/Http/Controllers/Api/ParkingMapController.php`

```php
public function index()
{
    $parkingAreas = AreaParkir::with(['transaksis' => function($query) {
        $query->where(function($q) {
            $q->whereNull('waktu_keluar')->where('status', 'masuk');
        })->orWhere(function($q) {
            $q->where('status', 'bookmarked')
              ->where('bookmarked_at', '>', Carbon::now()->subMinutes(10));
        });
    }, 'transaksis.kendaraan', 'transaksis.user'])->get();
    
    $mapData = $parkingAreas->map(function($area) {
        $status = 'empty';
        if ($occupiedTransaction) $status = 'occupied';
        elseif ($bookmarkedTransaction) $status = 'bookmarked';
        
        return [
            'id' => $area->id_area,
            'name' => $area->nama_area,
            'capacity' => $area->kapasitas,
            'occupied_count' => $area->transaksis->where('status', 'masuk')->count(),
            'status' => $status,
            'vehicle' => $vehicle,
        ];
    });
    
    return response()->json($mapData);
}
```

**Penjelasan:**
- Eager loading untuk optimasi query
- Status: empty, occupied, bookmarked
- Bookmark expire setelah 10 menit

---

## 10. Fase 7: Optimasi Form Check-in & Simplifikasi Pembayaran (23 Februari 2026)

### 10.1 Form Check-in Dual Mode

**File:** `resources/views/parkir/create.blade.php`

**Konsep:**
- Input plat nomor sebagai entry point utama
- Auto-check apakah plat terdaftar atau tidak
- Jika terdaftar → auto-fill, gunakan `id_kendaraan`
- Jika tidak terdaftar → tampilkan form kendaraan baru

**Alpine.js Logic:**
```javascript
function checkInForm() {
    return {
        platNomor: '',
        selectedVehicle: null,
        vehicleFound: false,
        isChecking: false,
        
        async checkPlat() {
            const plat = this.platNomor.trim();
            if (plat.length < 2) return;
            
            this.isChecking = true;
            const res = await fetch(`/api/kendaraan/check-plat?plat=${encodeURIComponent(plat)}`);
            const data = await res.json();
            
            if (data.found && data.kendaraan) {
                this.vehicleFound = true;
                this.selectedVehicle = data.kendaraan;
                this.autoSelectTarifByJenis(data.kendaraan.jenis_kendaraan);
            } else {
                this.vehicleFound = false;
                this.selectedVehicle = null;
            }
            this.isChecking = false;
        },
        
        autoSelectTarifByJenis(jenis) {
            const tarifSelect = this.$refs.idTarif;
            for (let opt of tarifSelect.options) {
                if (opt.dataset.jenis === jenis) {
                    tarifSelect.value = opt.value;
                    break;
                }
            }
        }
    };
}
```

**Form Structure:**
```html
<!-- Input Plat Nomor -->
<input type="text" id="plat_nomor" x-model="platNomor" @input.debounce.300ms="checkPlat()">

<!-- Hidden: id_kendaraan (hanya jika terdaftar) -->
<input type="hidden" :name="vehicleFound ? 'id_kendaraan' : ''" :value="selectedVehicle?.id_kendaraan">

<!-- Hidden: vehicle_mode -->
<input type="hidden" name="vehicle_mode" :value="vehicleFound ? 'existing' : 'new'">

<!-- Section: Kendaraan Baru (hanya jika tidak terdaftar) -->
<div x-show="!vehicleFound && platNomor.length >= 2">
    <input type="hidden" :name="vehicleFound ? '' : 'plat_nomor'" :value="platNomor">
    <select name="jenis_kendaraan" :required="!vehicleFound">...</select>
    <input name="warna" type="text">
    <input name="pemilik" type="text">
</div>
```

**Penjelasan:**
- **Debounce 300ms**: Menunggu user selesai mengetik sebelum check
- **Conditional Required**: Field kendaraan baru hanya required jika `!vehicleFound`
- **Auto-select Tarif**: Berdasarkan jenis kendaraan (terdaftar atau baru)

### 10.2 Controller Check-in dengan Dual Mode

**File:** `app/Http/Controllers/TransaksiController.php`

```php
public function checkIn(Request $request)
{
    $isNewVehicle = $request->filled('vehicle_mode') && $request->vehicle_mode === 'new';
    
    if ($isNewVehicle) {
        $request->validate([
            'plat_nomor' => 'required|string|max:15',
            'jenis_kendaraan' => 'required|string|max:20',
            'warna' => 'nullable|string|max:20',
            'pemilik' => 'nullable|string|max:100',
            'id_tarif' => 'required|exists:tb_tarif,id_tarif',
            'id_area' => 'required|exists:tb_area_parkir,id_area',
            'catatan' => 'nullable|string|max:255',
        ]);
        
        // Normalisasi plat nomor
        $platNormalized = $this->normalizePlatNomor($request->plat_nomor);
        
        // Cek plat belum terdaftar (case-insensitive, ignore spaces)
        if (Kendaraan::whereRaw('UPPER(REPLACE(plat_nomor, \' \', \'\')) = ?', [$platNormalized])->exists()) {
            return back()->withInput()->with('error', 'Plat nomor sudah terdaftar.');
        }
    } else {
        $request->validate([
            'id_kendaraan' => 'required|exists:tb_kendaraan,id_kendaraan',
            'id_tarif' => 'required|exists:tb_tarif,id_tarif',
            'id_area' => 'required|exists:tb_area_parkir,id_area',
            'catatan' => 'nullable|string|max:255',
        ]);
    }
    
    try {
        $transaksi = DB::transaction(function () use ($request, $isNewVehicle) {
            $area = AreaParkir::lockForUpdate()->findOrFail($request->id_area);
            
            if ($area->terisi >= $area->kapasitas) {
                throw new \Exception('Kapasitas area parkir sudah penuh');
            }
            
            $id_kendaraan = $request->id_kendaraan;
            
            if ($isNewVehicle) {
                // Buat kendaraan baru
                $platNormalized = $this->normalizePlatNomor($request->plat_nomor);
                $kendaraan = Kendaraan::create([
                    'plat_nomor' => $platNormalized,
                    'jenis_kendaraan' => $request->jenis_kendaraan,
                    'warna' => $request->warna,
                    'pemilik' => $request->pemilik,
                    'id_user' => null,
                ]);
                $id_kendaraan = $kendaraan->id_kendaraan;
            }
            
            $transaksi = Transaksi::create([
                'id_kendaraan' => $id_kendaraan,
                'id_tarif' => $request->id_tarif,
                'id_area' => $request->id_area,
                'id_user' => Auth::id(),
                'waktu_masuk' => Carbon::now(),
                'status' => 'masuk',
                'catatan' => $request->catatan,
            ]);
            
            $area->increment('terisi');
            
            return $transaksi;
        });
        
        return redirect()->route('transaksi.parkir.index')
            ->with('success', 'Kendaraan berhasil dicatat masuk parkir. ID Transaksi: ' . $transaksi->id_parkir);
    } catch (\Exception $e) {
        return back()->withInput()->with('error', 'Gagal mencatat transaksi: ' . $e->getMessage());
    }
}

private function normalizePlatNomor(string $plat): string
{
    return strtoupper(str_replace(' ', '', trim($plat)));
}
```

**Penjelasan:**
- **Dual Mode**: Validasi berbeda untuk existing vs new vehicle
- **Normalisasi Plat**: Uppercase, hapus spasi untuk konsistensi
- **Case-insensitive Check**: "B 1234" = "b1234" = "B1234"
- **Atomic Operation**: Semua dalam DB transaction dengan row lock

### 10.3 API Kendaraan Search

**File:** `app/Http/Controllers/Api/KendaraanSearchController.php`

```php
public function checkPlat(Request $request): JsonResponse
{
    $plat = trim($request->query('plat', ''));
    if (strlen($plat) < 2) {
        return response()->json(['found' => false, 'kendaraan' => null]);
    }
    
    $platNormalized = strtoupper(str_replace(' ', '', $plat));
    $kendaraan = Kendaraan::whereRaw('UPPER(REPLACE(plat_nomor, \' \', \'\')) = ?', [$platNormalized])->first();
    
    return response()->json([
        'found' => $kendaraan !== null,
        'kendaraan' => $kendaraan ? [
            'id_kendaraan' => $kendaraan->id_kendaraan,
            'plat_nomor' => $kendaraan->plat_nomor,
            'jenis_kendaraan' => $kendaraan->jenis_kendaraan,
            'warna' => $kendaraan->warna,
            'pemilik' => $kendaraan->pemilik,
        ] : null,
    ]);
}
```

**Penjelasan:**
- API untuk autocomplete dan check plat
- Normalisasi untuk matching yang fleksibel
- Return JSON untuk AJAX calls

### 10.4 Simplifikasi Pembayaran (Hapus Manual & QR)

**File:** `database/migrations/2026_02_22_211205_remove_manual_qr_payment_methods_from_database.php`

```php
public function up(): void
{
    // Hapus semua pembayaran dengan metode manual atau qr_scan
    DB::table('tb_pembayaran')
        ->whereIn('metode', ['manual', 'qr_scan'])
        ->delete();
    
    // Update transaksi yang terkait
    DB::table('tb_transaksi')
        ->whereIn('status_pembayaran', ['berhasil'])
        ->whereNotNull('id_pembayaran')
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('tb_pembayaran')
                ->whereColumn('tb_pembayaran.id_pembayaran', 'tb_transaksi.id_pembayaran');
        })
        ->update([
            'status_pembayaran' => 'pending',
            'id_pembayaran' => null,
        ]);
}
```

**Perubahan:**
- Hapus method `manual_confirm()`, `manual_process()`, `qr_scan()`, `confirm_qr_signed()`, `thankYou()`, `confirm_qr()` dari PaymentController
- Hapus route manual & QR
- Hapus view `manual-confirm.blade.php`, `qr-scan.blade.php`, `thank-you.blade.php`
- Update `payment.create.blade.php` - hanya tampilkan Midtrans
- Update model Pembayaran - hapus scope `manual()` dan `qr_scan()`, tambah `midtrans()`

**Alasan:**
- Midtrans sudah mencakup semua metode pembayaran (GoPay, OVO, DANA, Transfer Bank, Kartu Kredit)
- Simplifikasi kode dan maintenance
- Konsistensi: semua pembayaran via online gateway

---

## 11. Arsitektur Sistem

### 11.1 Struktur Folder

```
app/
├── Console/Commands/          # Artisan commands
├── Http/
│   ├── Controllers/
│   │   ├── Api/               # API Controllers (JSON responses)
│   │   │   ├── KendaraanSearchController.php
│   │   │   ├── ParkingMapController.php
│   │   │   └── PlateRecognizerController.php
│   │   ├── Auth/              # Authentication controllers
│   │   └── [Other Controllers]
│   └── Middleware/
│       └── RoleMiddleware.php  # RBAC middleware
├── Models/                     # Eloquent models
├── Observers/                  # Model observers
│   └── TransaksiObserver.php
├── Providers/
│   └── AppServiceProvider.php  # Service registration
└── Services/                   # Business logic layer
    └── PlateRecognizerService.php

database/
├── migrations/                 # Database schema changes
└── seeders/                   # Database seeders

resources/
├── views/
│   ├── components/            # Reusable Blade components
│   │   └── plate-scanner.blade.php
│   └── [Other views]
├── css/
│   └── app.css               # Tailwind CSS
└── js/
    └── app.js

routes/
└── web.php                    # All routes
```

### 11.2 Pola Arsitektur

**1. Service Layer Pattern**
- Business logic di Service class
- Controller hanya handle HTTP request/response
- Contoh: `PlateRecognizerService` → `PlateRecognizerController`

**2. Repository Pattern (Implicit)**
- Eloquent Models sebagai repository
- Query logic di Model atau Controller (bisa dipindah ke Repository jika perlu)

**3. Observer Pattern**
- `TransaksiObserver` untuk auto-logging
- Event-driven untuk side effects

**4. Middleware Pattern**
- `RoleMiddleware` untuk authorization
- `auth` middleware untuk authentication

### 11.3 Database Relationships

```
User (1) ----< Kendaraan (M)        [id_user nullable]
User (1) ----< Transaksi (M)       [id_user = operator]
User (1) ----< Pembayaran (M)      [id_user = petugas, nullable]
User (1) ----< LogAktifitas (M)

Kendaraan (1) ----< Transaksi (M)
AreaParkir (1) ----< Transaks