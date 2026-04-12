# Dokumentasi Rekayasa Proyek NESTON
## Sistem Manajemen Parkir Berbasis Web
### Periode: 14 Januari 2026 – 23 Februari 2026

---

## Daftar Isi

1. [Ringkasan Proyek](#1-ringkasan-proyek)
2. [Teknologi & Stack](#2-teknologi--stack)
3. [Timeline Pengembangan](#3-timeline-pengembangan)
4. [Fase 1: Inisialisasi & Fondasi Database (14 Januari 2026)](#4-fase-1-inisialisasi--fondasi-database-14-januari-2026)
5. [Fase 2: Role-Based Access Control (15 Januari 2026)](#5-fase-2-role-based-access-control-15-januari-2026)
6. [Fase 3: Sistem Pembayaran (18–19 Januari 2026)](#6-fase-3-sistem-pembayaran-18-19-januari-2026)
7. [Fase 4: Peningkatan Arsitektur (22 Januari 2026)](#7-fase-4-peningkatan-arsitektur-22-januari-2026)
8. [Fase 5: Integrasi Midtrans & Fitur Lanjutan (29 Januari – 11 Februari 2026)](#8-fase-5-integrasi-midtrans--fitur-lanjutan-29-januari--11-februari-2026)
9. [Fase 6: Plate Recognizer & Peta Parkir (Februari 2026)](#9-fase-6-plate-recognizer--peta-parkir-februari-2026)
10. [Fase 7: Optimasi Form Check-in & Simplifikasi Pembayaran (23 Februari 2026)](#10-fase-7-optimasi-form-check-in--simplifikasi-pembayaran-23-februari-2026)
11. [Arsitektur Sistem](#11-arsitektur-sistem)
12. [Diagram Alur Bisnis](#12-diagram-alur-bisnis)
13. [Lampiran: Entity-Relationship Diagram (ERD)](#13-lampiran-entity-relationship-diagram-erd)
14. [Lampiran: Lokasi File Penting](#14-lampiran-lokasi-file-penting)

---

## 1. Ringkasan Proyek

**NESTON** adalah sistem manajemen parkir berbasis web yang dibangun menggunakan **Laravel 12** dan **Tailwind CSS**. Sistem ini dirancang untuk mengelola operasional parkir secara digital dengan fitur-fitur modern dan efisien.

### Fitur Utama

- Check-in/Check-out kendaraan dengan manajemen kapasitas real-time
- Pembayaran online via Midtrans (GoPay, OVO, DANA, Transfer Bank, Kartu Kredit)
- Scan plat nomor otomatis dengan Plate Recognizer API
- Form check-in fleksibel — mendukung kendaraan terdaftar dan kendaraan baru
- Role-Based Access Control (Admin, Petugas, Owner, User)
- Peta parkir interaktif dengan fitur bookmark slot
- Laporan transaksi & pembayaran dengan export CSV
- Log aktivitas untuk audit trail
- Dashboard dengan statistik real-time

---

## 2. Teknologi & Stack

### Backend
- **Framework:** Laravel 12 (PHP 8.2+)
- **Database:** MySQL/MariaDB
- **ORM:** Eloquent
- **Payment Gateway:** Midtrans PHP SDK
- **API Integration:** Plate Recognizer API

### Frontend
- **CSS Framework:** Tailwind CSS 4.1.18
- **JavaScript:** Alpine.js 3.x (via CDN)
- **Build Tool:** Vite 7.0.7
- **Icons:** SVG inline

### Tools & Dependencies
- **Composer:** Dependency management (PHP)
- **NPM:** Frontend dependencies
- **Git:** Version control

---

## 3. Timeline Pengembangan

| Tanggal | Commit | Fase | Fitur / Perubahan |
|---------|--------|------|-------------------|
| 14 Jan 2026 | Initial commit | **Fondasi** | Setup Laravel 12, struktur database dasar |
| 14 Jan 2026 | first push | **Database** | Tabel User, Kendaraan, Area, Tarif, Transaksi, Log |
| 15 Jan 2026 | third push | **RBAC** | Kolom `role` pada User, RoleMiddleware |
| 18 Jan 2026 | third push | **Pembayaran** | Tabel Pembayaran, relasi ke Transaksi |
| 19 Jan 2026 | forth push | **Refactor** | Cleanup tabel pembayaran duplikat |
| 22 Jan 2026 | sixth/seventh push | **Arsitektur** | Soft Deletes, kolom Catatan, TransaksiObserver |
| 28 Jan 2026 | nineth push | **UI/UX** | Perbaikan desain form di semua fitur |
| 29 Jan 2026 | tenth push | **Midtrans** | Kolom Midtrans di Pembayaran, Bookmark slot |
| 29 Jan 2026 | — | **Fleksibilitas** | Field kendaraan dibuat nullable |
| 11 Feb 2026 | sixteenth push | **Midtrans** | `midtrans_order_id` pada Transaksi |
| Feb 2026 | — | **Integrasi** | Plate Recognizer API, Peta Parkir |
| 22 Feb 2026 | — | **Cleanup** | Hapus pembayaran manual & QR, hanya Midtrans |
| 23 Feb 2026 | — | **Optimasi** | Form check-in baru dengan dual mode |

---

## 4. Fase 1: Inisialisasi & Fondasi Database (14 Januari 2026)

### 4.1 Setup Proyek Laravel

**Commit:** `ad3fc9e` — Initial commit

Proyek dimulai dengan Laravel 12. Struktur dasar dibuat dengan:
- `composer.json` untuk Laravel 12
- `package.json` dengan Tailwind CSS 4 dan Vite
- Konfigurasi database MySQL

---

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
- Tabel dasar untuk autentikasi pengguna.
- `email` bersifat `unique` untuk mencegah duplikasi akun.
- `rememberToken` dipakai untuk fitur "Ingat Saya".
- `timestamps()` mencatat `created_at` dan `updated_at` secara otomatis.

**Model:** `app/Models/User.php`
- Menggunakan sistem autentikasi bawaan Laravel.
- Relasi: `hasMany` ke Kendaraan, Transaksi, Pembayaran, LogAktifitas.

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
- Keunikan `plat_nomor` dijamin di level aplikasi, bukan constraint database (agar lebih fleksibel untuk normalisasi).
- `id_user` bersifat nullable (diubah di migration 29 Jan) untuk mendukung kendaraan tamu.
- Foreign key ke `tb_user` dengan `cascade delete`.

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
- `kapasitas`: total slot parkir yang tersedia.
- `terisi`: jumlah slot yang sedang digunakan secara real-time.
- Tidak menggunakan soft delete karena area parkir tidak dihapus secara operasional.
- Saat check-in: `terisi++` (dengan row lock untuk mencegah race condition).
- Saat check-out: `terisi--` (dengan validasi nilai > 0).

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
- Tarif per jam ditetapkan berdasarkan jenis kendaraan: motor, mobil, atau lainnya.
- `tarif_perjam` disimpan dalam format decimal untuk presisi.

**Perhitungan Biaya:**
```php
$durasi_jam = ceil($waktu_keluar->diffInMinutes($waktu_masuk) / 60);
$biaya_total = $durasi_jam * $tarif->tarif_perjam;
```
- Durasi dibulatkan ke atas (`ceil`) dengan minimum 1 jam.
- Contoh: 1 jam 5 menit dihitung sebagai 2 jam.

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

    $table->foreign('id_kendaraan')->references('id_kendaraan')->on('tb_kendaraan')->onDelete('cascade');
    $table->foreign('id_tarif')->references('id_tarif')->on('tb_tarif')->onDelete('cascade');
    $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('cascade');
    $table->foreign('id_area')->references('id_area')->on('tb_area_parkir')->onDelete('cascade');
});
```

**Penjelasan:**
- `id_parkir` digunakan sebagai primary key (bukan `id` default Laravel).
- `waktu_keluar`, `durasi_jam`, dan `biaya_total` bersifat nullable karena diisi saat proses checkout.
- `status`: nilai `'masuk'` menandai parkir aktif, `'keluar'` menandai transaksi selesai.
- `id_user` mencatat operator yang melakukan check-in.
- Semua foreign key menggunakan `cascade delete` untuk menjaga integritas data.

**Model dengan Accessor (Auto-calculate Biaya):**
```php
public function getBiayaTotalAttribute() {
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
- Berfungsi sebagai audit trail untuk semua aktivitas penting di sistem.
- Mencatat informasi: siapa (user), apa (aktivitas), dan kapan (waktu).
- Digunakan untuk keperluan compliance dan troubleshooting.

---

## 5. Fase 2: Role-Based Access Control (15 Januari 2026)

### 5.1 Kolom Role pada User

**File:** `database/migrations/2026_01_15_000001_add_role_to_tb_user.php`

```php
Schema::table('tb_user', function (Blueprint $table) {
    $table->string('role')->default('user')->after('password');
});
```

**Role yang tersedia:**
- `admin`: Akses penuh (CRUD semua data, akses log aktivitas)
- `petugas`: Akses operasional (check-in/out, pembayaran)
- `owner`: Akses laporan & analitik
- `user`: Akses default (terbatas)

---

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
- Middleware ini memvalidasi role user sebelum mengizinkan akses ke suatu route.
- Mendukung beberapa role sekaligus: `->middleware(['role:admin,petugas'])`.
- Perbandingan dilakukan secara case-insensitive untuk fleksibilitas.
- Mengembalikan HTTP 403 jika user tidak memiliki role yang sesuai.

**Registrasi Middleware:** `bootstrap/app.php`
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

---

## 6. Fase 3: Sistem Pembayaran (18–19 Januari 2026)

### 6.1 Tabel Pembayaran (`tb_pembayaran`)

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
- `id_parkir`: menghubungkan pembayaran ke transaksi parkir (relasi one-to-one).
- `nominal`: jumlah yang dibayarkan (bisa berbeda dari `biaya_total` jika ada diskon).
- `metode`: metode pembayaran (`manual`, `qr_scan`, dan kemudian `midtrans`).
- `status`: status pembayaran (`pending`, `berhasil`, `gagal`).
- `id_user`: petugas yang memproses pembayaran (nullable untuk pembayaran otomatis).

---

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
- `status_pembayaran` di tabel transaksi memudahkan filter transaksi yang belum/sudah dibayar.
- `id_pembayaran` menyimpan referensi langsung ke record pembayaran.
- `onDelete('set null')` memastikan transaksi tetap ada meskipun record pembayaran dihapus.

---

### 6.3 Cleanup Tabel Duplikat

**File:** `database/migrations/2026_01_19_000001_cleanup_pembayarans_table.php`

Migration ini menghapus tabel `pembayarans` (versi plural yang duplikat) dan memastikan hanya `tb_pembayaran` yang digunakan sebagai standar.

---

## 7. Fase 4: Peningkatan Arsitektur (22 Januari 2026)

### 7.1 Soft Deletes

**File:** `database/migrations/2026_01_22_000001_add_soft_delete_to_tables.php`

```php
if (Schema::hasTable('tb_user') && !Schema::hasColumn('tb_user', 'deleted_at')) {
    Schema::table('tb_user', function (Blueprint $table) {
        $table->softDeletes();
    });
}
// Diterapkan juga pada: tb_kendaraan, tb_transaksi, tb_pembayaran
```

**Penjelasan:**
- Soft delete menambahkan kolom `deleted_at` pada tabel.
- Data tidak dihapus secara fisik dari database, hanya ditandai dengan timestamp `deleted_at`.
- Berguna untuk keperluan audit, pemulihan data, dan compliance.
- Di Model: cukup tambahkan `use SoftDeletes;`.

**Cara penggunaan query:**
```php
Kendaraan::all();                 // Hanya data yang aktif (tidak dihapus)
Kendaraan::withTrashed()->get();  // Termasuk data yang sudah dihapus
Kendaraan::onlyTrashed()->get();  // Hanya data yang sudah dihapus
```

---

### 7.2 Kolom Catatan pada Transaksi

**File:** `database/migrations/2026_01_22_000002_add_catatan_to_tb_transaksi_table.php`

Penambahan kolom `catatan` memungkinkan petugas menambahkan informasi tambahan per transaksi, seperti kondisi kendaraan, nomor karcis manual, atau keterangan khusus lainnya.

---

### 7.3 TransaksiObserver (Auto-Logging)

**File:** `app/Observers/TransaksiObserver.php`

```php
class TransaksiObserver
{
    public function created(Transaksi $transaksi): void
    {
        if (Auth::check()) {
            LogAktifitas::create([
                'id_user'          => Auth::id(),
                'aktivitas'        => 'Membuat transaksi parkir #' . str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT),
                'waktu_aktivitas'  => Carbon::now(),
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
                'id_user'         => Auth::id(),
                'aktivitas'       => $activity,
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
- Observer dipanggil secara otomatis setiap kali model `Transaksi` dibuat, diperbarui, atau dihapus.
- Setiap aksi dicatat ke tabel `tb_log_aktivitas` tanpa perlu memanggil secara manual dari controller.
- `isDirty('status')` digunakan untuk mendeteksi apakah field `status` berubah sebelum disimpan.

---

## 8. Fase 5: Integrasi Midtrans & Fitur Lanjutan (29 Januari – 11 Februari 2026)

### 8.1 Kolom Midtrans di Tabel Pembayaran

**File:** `database/migrations/2026_01_29_100000_add_midtrans_fields_to_tb_pembayaran.php`

```php
Schema::table('tb_pembayaran', function (Blueprint $table) {
    $table->string('order_id', 64)->nullable()->after('id_parkir');
    $table->string('transaction_id', 64)->nullable()->after('order_id');
    $table->string('payment_type', 32)->nullable()->after('transaction_id');
});

// Ubah enum ke VARCHAR agar mendukung nilai tambahan dari Midtrans
DB::statement("ALTER TABLE tb_pembayaran MODIFY metode VARCHAR(50) DEFAULT 'manual'");
DB::statement("ALTER TABLE tb_pembayaran MODIFY status VARCHAR(50) DEFAULT 'pending'");
```

**Penjelasan:**
- `order_id`: ID order yang dibuat saat transaksi dimulai di Midtrans (format: `PARKIR-{id_parkir}-{timestamp}`).
- `transaction_id`: ID transaksi pembayaran yang dikembalikan oleh Midtrans setelah pembayaran berhasil.
- `payment_type`: tipe metode pembayaran yang digunakan (`bank_transfer`, `e_wallet`, `credit_card`, dll.).
- Kolom `metode` dan `status` diubah dari `ENUM` ke `VARCHAR` agar dapat menampung nilai dinamis dari Midtrans.

---

### 8.2 Midtrans Order ID di Tabel Transaksi

**File:** `database/migrations/2026_02_11_000001_add_midtrans_order_id_to_tb_transaksi.php`

```php
Schema::table('tb_transaksi', function (Blueprint $table) {
    $table->string('midtrans_order_id', 100)->nullable()->after('id_pembayaran');
});
```

**Penjelasan:**
- Menyimpan `order_id` Midtrans langsung di tabel transaksi untuk keperluan sinkronisasi status pembayaran.
- Digunakan sebagai fallback saat webhook Midtrans tidak diterima (misalnya saat pengembangan di localhost).
- Ketika user membuka halaman success, sistem dapat memeriksa status langsung ke API Midtrans menggunakan nilai ini.

---

### 8.3 Alur Pembayaran Midtrans

**File:** `app/Http/Controllers/PaymentController.php`

#### a) Generate Snap Token

```php
public function midtransSnapToken(Request $request, $id_parkir)
{
    $transaksi = Transaksi::with(['kendaraan', 'tarif'])->findOrFail($id_parkir);

    if ($transaksi->status_pembayaran === 'berhasil') {
        return response()->json(['error' => 'Transaksi sudah dibayar'], 400);
    }

    \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
    \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
    \Midtrans\Config::$isSanitized  = true;
    \Midtrans\Config::$is3ds        = true;

    $order_id     = 'PARKIR-' . $id_parkir . '-' . time();
    $gross_amount = (int) round((float) $transaksi->biaya_total);

    $transaksi->update(['midtrans_order_id' => $order_id]);

    $params = [
        'transaction_details' => [
            'order_id'     => $order_id,
            'gross_amount' => $gross_amount,
        ],
        'item_details' => [[
            'id'       => (string) $id_parkir,
            'price'    => $gross_amount,
            'quantity' => 1,
            'name'     => 'Parkir - ' . ($transaksi->kendaraan->plat_nomor ?? 'Kendaraan'),
            'category' => 'Parkir',
        ]],
        'customer_details' => [
            'first_name' => $transaksi->kendaraan->pemilik ?? $transaksi->kendaraan->plat_nomor ?? 'Customer',
            'email'      => $transaksi->user?->email ?? 'customer@parked.local',
        ],
        'callbacks' => [
            'finish'   => route('payment.midtrans.finish', $id_parkir),
            'unfinish' => route('payment.midtrans.unfinish', $id_parkir),
            'error'    => route('payment.midtrans.error', $id_parkir),
        ],
    ];

    $snapToken = \Midtrans\Snap::getSnapToken($params);

    return response()->json([
        'snap_token' => $snapToken,
        'order_id'   => $order_id,
    ]);
}
```

**Penjelasan:**
- Snap Token digunakan oleh frontend untuk menampilkan halaman pembayaran Midtrans secara overlay.
- `order_id` yang unik dibuat dengan format `PARKIR-{id_parkir}-{timestamp}` untuk memudahkan identifikasi.
- Callback URLs menentukan halaman tujuan redirect setelah pembayaran selesai, gagal, atau dibatalkan.

---

#### b) Webhook Notifikasi (Idempotent)

```php
public function midtransNotification(Request $request)
{
    $payload  = $request->all();
    $order_id = $payload['order_id'] ?? null;

    if (!$order_id || !preg_match('/^PARKIR-(\d+)-/', $order_id, $m)) {
        return response()->json(['message' => 'Invalid order_id'], 400);
    }

    $id_parkir = (int) $m[1];

    \Midtrans\Config::$serverKey    = config('services.midtrans.server_key');
    \Midtrans\Config::$isProduction = config('services.midtrans.is_production');

    $statusResponse     = \Midtrans\Transaction::status($order_id);
    $transaction_status = strtolower($statusResponse->transaction_status ?? '');

    if (!in_array($transaction_status, ['capture', 'settlement'])) {
        return response()->json(['received' => true]);
    }

    $this->applyMidtransSuccess($id_parkir, $order_id, ...);

    return response()->json(['received' => true]);
}
```

**Penjelasan:**
- **Idempotent**: endpoint ini aman dipanggil berkali-kali tanpa menyebabkan duplikasi data.
- **Verifikasi via API**: status pembayaran selalu dikonfirmasi langsung ke Midtrans, bukan hanya berdasarkan body POST yang diterima, untuk mencegah manipulasi.
- Hanya status `capture` dan `settlement` yang dianggap sebagai pembayaran berhasil.

---

#### c) Apply Midtrans Success (Idempotent)

```php
private function applyMidtransSuccess(int $id_parkir, string $order_id, ?string $transaction_id, ?string $payment_type, float $gross_amount): void
{
    DB::transaction(function () use ($id_parkir, $order_id, $transaction_id, $payment_type, $gross_amount) {
        $transaksi = Transaksi::lockForUpdate()->find($id_parkir);

        // Idempotent check — skip jika sudah diproses
        if (!$transaksi || $transaksi->status_pembayaran === 'berhasil') {
            return;
        }

        if ($transaksi->status !== 'keluar' || is_null($transaksi->biaya_total)) {
            return; // State tidak valid
        }

        $pembayaran = Pembayaran::create([
            'id_parkir'        => $id_parkir,
            'order_id'         => $order_id,
            'transaction_id'   => $transaction_id,
            'payment_type'     => $payment_type,
            'nominal'          => $gross_amount,
            'metode'           => 'midtrans',
            'status'           => 'berhasil',
            'keterangan'       => 'Pembayaran Midtrans (' . ($payment_type ?? 'online') . ')',
            'id_user'          => null,
            'waktu_pembayaran' => Carbon::now(),
        ]);

        $transaksi->update([
            'status_pembayaran' => 'berhasil',
            'id_pembayaran'     => $pembayaran->id_pembayaran,
        ]);
    });
}
```

**Penjelasan:**
- `lockForUpdate()` digunakan untuk mengunci baris saat proses pembayaran berlangsung, mencegah double-payment.
- Cek idempotent dilakukan sebelum operasi apapun untuk memastikan tidak ada duplikasi.
- Seluruh operasi dijalankan dalam satu DB transaction agar bersifat atomic (semua berhasil atau semua dibatalkan).

---

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

    $statusResponse     = \Midtrans\Transaction::status($order_id);
    $transaction_status = strtolower($statusResponse->transaction_status ?? '');

    if (in_array($transaction_status, ['capture', 'settlement'])) {
        $this->applyMidtransSuccess($id_parkir, $order_id, ...);
        return true;
    }

    return false;
}
```

**Penjelasan:**
- Fungsi ini dipanggil saat user membuka halaman success setelah melakukan pembayaran.
- Berfungsi sebagai mekanisme fallback ketika webhook dari Midtrans tidak berhasil diterima server (misalnya karena pengembangan di localhost).
- Memastikan status pembayaran selalu tersinkronisasi meskipun webhook tidak berjalan.

---

### 8.4 Fitur Bookmark Slot Parkir

**File:** `database/migrations/2026_01_29_023655_add_bookmarked_status_to_transaksis_table.php`

```php
Schema::table('tb_transaksi', function (Blueprint $table) {
    $table->enum('status', ['masuk', 'keluar', 'bookmarked'])->change();
    $table->dateTime('bookmarked_at')->nullable()->after('status');
});
```

**Penjelasan:**
- Status `bookmarked` menandai slot yang sedang dipesan sementara oleh pengguna.
- `bookmarked_at` mencatat waktu pemesanan, digunakan untuk timer expire (10 menit).
- Slot yang sedang dibookmark tidak dapat digunakan oleh pengguna lain hingga waktu habis.

**API Bookmark:** `app/Http/Controllers/Api/ParkingMapController.php`
```php
public function bookmark(Request $request, $area_id)
{
    $existingActiveTransaction = Transaksi::where('id_area', $area_id)
        ->where(function($query) {
            $query->whereNull('waktu_keluar')
                  ->where('status', 'masuk');
        })->orWhere(function($query) {
            $query->where('status', 'bookmarked')
                  ->where('bookmarked_at', '>', Carbon::now()->subMinutes(10));
        })->first();

    if ($existingActiveTransaction) {
        return response()->json(['message' => 'Slot parkir ini sedang terisi atau sudah dibookmark.'], 409);
    }

    $transaksi = Transaksi::create([
        'id_kendaraan' => null,
        'waktu_masuk'  => null,
        'id_tarif'     => null,
        'status'       => 'bookmarked',
        'id_user'      => Auth::id(),
        'id_area'      => $area_id,
        'bookmarked_at'=> Carbon::now(),
    ]);

    return response()->json(['message' => 'Slot berhasil dibookmark.', 'transaksi' => $transaksi], 200);
}
```

---

### 8.5 Kendaraan Fields Nullable

**File:** `database/migrations/2026_01_29_000001_make_tb_kendaraan_fields_nullable.php`

Kolom `id_user`, `warna`, dan `pemilik` pada `tb_kendaraan` diubah menjadi nullable. Hal ini memungkinkan proses check-in tetap berjalan meskipun data kendaraan belum lengkap (untuk kendaraan tamu yang tidak terdaftar).

---

## 9. Fase 6: Plate Recognizer & Peta Parkir (Februari 2026)

### 9.1 Plate Recognizer Service

**File:** `app/Services/PlateRecognizerService.php`

```php
class PlateRecognizerService
{
    private string $apiUrl              = 'https://api.platerecognizer.com/v1/plate-reader/';
    private string $apiKey;
    private float  $confidenceThreshold = 0.80; // Minimal 80%

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
                'confidence'   => 0,
                'valid'        => false,
                'message'      => 'Tidak ada plat nomor yang terdeteksi dalam gambar',
            ];
        }

        $firstResult  = $data['results'][0];
        $plateNumber  = $firstResult['plate'] ?? null;
        $confidence   = floatval($firstResult['score'] ?? 0);
        $isValid      = $confidence >= $this->confidenceThreshold;

        return [
            'plate_number' => $plateNumber,
            'confidence'   => $confidence,
            'valid'        => $isValid,
            'message'      => $isValid
                ? 'Plat nomor berhasil dideteksi'
                : 'Plat tidak valid (confidence di bawah 80%)',
        ];
    }
}
```

**Penjelasan:**
- Menggunakan Service Layer Pattern — logika API dipisahkan dari Controller.
- API key disimpan di `.env`, tidak pernah dikirimkan ke frontend.
- Threshold confidence 80% digunakan sebagai standar validasi hasil deteksi.
- Error handling mencakup kegagalan koneksi API maupun hasil deteksi yang kosong.

---

### 9.2 Plate Recognizer Controller

**File:** `app/Http/Controllers/Api/PlateRecognizerController.php`

```php
public function scanPlate(Request $request): JsonResponse
{
    $request->validate([
        'image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:5120'], // Maksimal 5MB
    ]);

    $image  = $request->file('image');
    $result = $this->plateRecognizerService->scanPlate($image, $request->boolean('debug'));

    return response()->json([
        'success'      => true,
        'plate_number' => $result['plate_number'],
        'confidence'   => $result['confidence'],
        'valid'        => $result['valid'],
        'message'      => $result['message'],
    ]);
}
```

**Penjelasan:**
- Controller hanya bertanggung jawab menerima request dan mengembalikan response (separation of concerns).
- Validasi file: format harus JPG atau PNG, maksimal 5MB.
- Seluruh logika bisnis berada di `PlateRecognizerService`.

---

### 9.3 Komponen Kamera Frontend

**File:** `resources/views/components/plate-scanner.blade.php`

**Fitur:**
- Menggunakan `getUserMedia` dengan `facingMode: 'environment'` untuk mengakses kamera belakang perangkat.
- Alur: Buka Kamera → Ambil Foto → Scan Plat → Ambil Ulang (jika perlu).
- Upload gambar via `fetch()` ke endpoint `/scan-plate`.
- Auto-fill input plat nomor jika hasil deteksi valid.
- Loading indicator dan pesan error/sukses.

**JavaScript Logic:**
```javascript
async scanPlate() {
    const response  = await fetch(this.capturedImage);
    const blob      = await response.blob();
    const formData  = new FormData();
    formData.append('image', blob, 'plate-image.jpg');

    const scanResponse = await fetch('{{ route("api.scan-plate") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN'     : document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'X-Requested-With' : 'XMLHttpRequest',
        },
        body: formData
    });

    const data = await scanResponse.json();

    if (data.valid && data.plate_number) {
        this.fillTargetInput(data.plate_number);
    }
}
```

---

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
        if ($occupiedTransaction)  $status = 'occupied';
        elseif ($bookmarkedTransaction) $status = 'bookmarked';

        return [
            'id'             => $area->id_area,
            'name'           => $area->nama_area,
            'capacity'       => $area->kapasitas,
            'occupied_count' => $area->transaksis->where('status', 'masuk')->count(),
            'status'         => $status,
            'vehicle'        => $vehicle,
        ];
    });

    return response()->json($mapData);
}
```

**Penjelasan:**
- Menggunakan eager loading (`with(...)`) untuk mengoptimalkan query database.
- Status slot: `empty` (kosong), `occupied` (terisi), atau `bookmarked` (dipesan sementara).
- Bookmark dianggap kadaluarsa setelah 10 menit dan tidak lagi dihitung sebagai occupied.

---

## 10. Fase 7: Optimasi Form Check-in & Simplifikasi Pembayaran (23 Februari 2026)

### 10.1 Form Check-in Dual Mode

**File:** `resources/views/parkir/create.blade.php`

**Konsep:**
- Input plat nomor menjadi entry point utama.
- Sistem secara otomatis mengecek apakah plat sudah terdaftar di database.
- Jika terdaftar → data kendaraan otomatis terisi, menggunakan `id_kendaraan`.
- Jika tidak terdaftar → form kendaraan baru ditampilkan untuk diisi petugas.

**Alpine.js Logic:**
```javascript
function checkInForm() {
    return {
        platNomor      : '',
        selectedVehicle: null,
        vehicleFound   : false,
        isChecking     : false,

        async checkPlat() {
            const plat = this.platNomor.trim();
            if (plat.length < 2) return;

            this.isChecking = true;
            const res  = await fetch(`/api/kendaraan/check-plat?plat=${encodeURIComponent(plat)}`);
            const data = await res.json();

            if (data.found && data.kendaraan) {
                this.vehicleFound    = true;
                this.selectedVehicle = data.kendaraan;
                this.autoSelectTarifByJenis(data.kendaraan.jenis_kendaraan);
            } else {
                this.vehicleFound    = false;
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

**Penjelasan:**
- **Debounce 300ms**: Pengecekan API ditunda 300ms setelah user berhenti mengetik, mengurangi request yang tidak perlu.
- **Conditional Required**: Field kendaraan baru hanya bersifat required jika `vehicleFound === false`.
- **Auto-select Tarif**: Tarif otomatis dipilih berdasarkan jenis kendaraan yang terdeteksi.

---

### 10.2 Controller Check-in dengan Dual Mode

**File:** `app/Http/Controllers/TransaksiController.php`

```php
public function checkIn(Request $request)
{
    $isNewVehicle = $request->filled('vehicle_mode') && $request->vehicle_mode === 'new';

    if ($isNewVehicle) {
        $request->validate([
            'plat_nomor'      => 'required|string|max:15',
            'jenis_kendaraan' => 'required|string|max:20',
            'warna'           => 'nullable|string|max:20',
            'pemilik'         => 'nullable|string|max:100',
            'id_tarif'        => 'required|exists:tb_tarif,id_tarif',
            'id_area'         => 'required|exists:tb_area_parkir,id_area',
            'catatan'         => 'nullable|string|max:255',
        ]);

        $platNormalized = $this->normalizePlatNomor($request->plat_nomor);

        if (Kendaraan::whereRaw('UPPER(REPLACE(plat_nomor, \' \', \'\')) = ?', [$platNormalized])->exists()) {
            return back()->withInput()->with('error', 'Plat nomor sudah terdaftar.');
        }
    } else {
        $request->validate([
            'id_kendaraan' => 'required|exists:tb_kendaraan,id_kendaraan',
            'id_tarif'     => 'required|exists:tb_tarif,id_tarif',
            'id_area'      => 'required|exists:tb_area_parkir,id_area',
            'catatan'      => 'nullable|string|max:255',
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
                $platNormalized = $this->normalizePlatNomor($request->plat_nomor);
                $kendaraan      = Kendaraan::create([
                    'plat_nomor'      => $platNormalized,
                    'jenis_kendaraan' => $request->jenis_kendaraan,
                    'warna'           => $request->warna,
                    'pemilik'         => $request->pemilik,
                    'id_user'         => null,
                ]);
                $id_kendaraan = $kendaraan->id_kendaraan;
            }

            $transaksi = Transaksi::create([
                'id_kendaraan' => $id_kendaraan,
                'id_tarif'     => $request->id_tarif,
                'id_area'      => $request->id_area,
                'id_user'      => Auth::id(),
                'waktu_masuk'  => Carbon::now(),
                'status'       => 'masuk',
                'catatan'      => $request->catatan,
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
- **Dual Mode**: Validasi berbeda untuk kendaraan terdaftar (existing) dan kendaraan baru.
- **Normalisasi Plat**: Uppercase dan penghapusan spasi untuk konsistensi penyimpanan.
- **Case-insensitive Check**: "B 1234", "b1234", dan "B1234" dianggap sama.
- **Atomic Operation**: Semua operasi (cek kapasitas, buat kendaraan, buat transaksi, update kapasitas) berjalan dalam satu DB transaction dengan row lock.

---

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
    $kendaraan      = Kendaraan::whereRaw('UPPER(REPLACE(plat_nomor, \' \', \'\')) = ?', [$platNormalized])->first();

    return response()->json([
        'found'     => $kendaraan !== null,
        'kendaraan' => $kendaraan ? [
            'id_kendaraan'    => $kendaraan->id_kendaraan,
            'plat_nomor'      => $kendaraan->plat_nomor,
            'jenis_kendaraan' => $kendaraan->jenis_kendaraan,
            'warna'           => $kendaraan->warna,
            'pemilik'         => $kendaraan->pemilik,
        ] : null,
    ]);
}
```

**Penjelasan:**
- Endpoint API untuk autocomplete dan pengecekan plat nomor secara real-time.
- Normalisasi dilakukan agar pencarian bersifat fleksibel (case-insensitive, ignore spasi).
- Mengembalikan respons JSON untuk diproses oleh AJAX call dari frontend.

---

### 10.4 Simplifikasi Pembayaran (Hapus Manual & QR)

**File:** `database/migrations/2026_02_22_211205_remove_manual_qr_payment_methods_from_database.php`

```php
public function up(): void
{
    DB::table('tb_pembayaran')
        ->whereIn('metode', ['manual', 'qr_scan'])
        ->delete();

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
            'id_pembayaran'     => null,
        ]);
}
```

**Perubahan yang dilakukan:**
- Hapus method `manual_confirm()`, `manual_process()`, `qr_scan()`, `confirm_qr_signed()`, `thankYou()`, dan `confirm_qr()` dari `PaymentController`.
- Hapus route yang berkaitan dengan pembayaran manual & QR.
- Hapus view: `manual-confirm.blade.php`, `qr-scan.blade.php`, `thank-you.blade.php`.
- Update `payment.create.blade.php` — hanya menampilkan opsi Midtrans.
- Update model Pembayaran — hapus scope `manual()` dan `qr_scan()`, tambahkan scope `midtrans()`.

**Alasan:**
- Midtrans sudah mencakup semua metode pembayaran yang dibutuhkan (GoPay, OVO, DANA, Transfer Bank, Kartu Kredit).
- Simplifikasi codebase untuk kemudahan maintenance.
- Konsistensi: seluruh transaksi pembayaran kini menggunakan satu gateway yang sama.

---

## 11. Arsitektur Sistem

### 11.1 Struktur Folder

```
app/
├── Console/Commands/           # Artisan commands
├── Http/
│   ├── Controllers/
│   │   ├── Api/                # API Controllers (JSON responses)
│   │   │   ├── KendaraanSearchController.php
│   │   │   ├── ParkingMapController.php
│   │   │   └── PlateRecognizerController.php
│   │   ├── Auth/               # Authentication controllers
│   │   └── [Other Controllers]
│   └── Middleware/
│       └── RoleMiddleware.php  # RBAC middleware
├── Models/                     # Eloquent models
├── Observers/                  # Model observers
│   └── TransaksiObserver.php
├── Providers/
│   └── AppServiceProvider.php  # Service & observer registration
└── Services/                   # Business logic layer
    └── PlateRecognizerService.php

database/
├── migrations/                 # Perubahan skema database
└── seeders/                    # Seeder data awal

resources/
├── views/
│   ├── components/             # Reusable Blade components
│   │   └── plate-scanner.blade.php
│   └── [Other views]
├── css/
│   └── app.css                 # Tailwind CSS
└── js/
    └── app.js

routes/
└── web.php                     # Seluruh definisi route
```

---

### 11.2 Pola Arsitektur yang Digunakan

**1. Service Layer Pattern**
Business logic ditempatkan di kelas Service terpisah. Controller hanya menangani request dan response HTTP. Contoh: `PlateRecognizerService` dipanggil oleh `PlateRecognizerController`.

**2. Repository Pattern (Implicit)**
Eloquent Models berperan sebagai repository. Query logic ditulis di Model atau Controller, dan dapat dipindahkan ke kelas Repository tersendiri jika dibutuhkan.

**3. Observer Pattern**
`TransaksiObserver` digunakan untuk auto-logging. Setiap event pada model (created, updated, deleted) memicu pencatatan log secara otomatis tanpa harus menuliskan kode logging di setiap controller.

**4. Middleware Pattern**
`RoleMiddleware` menangani otorisasi berbasis role. Middleware `auth` bawaan Laravel menangani autentikasi.

---

### 11.3 Database Relationships

```
User (1) ----< Kendaraan (M)       [id_user nullable]
User (1) ----< Transaksi (M)       [id_user = operator]
User (1) ----< Pembayaran (M)      [id_user = petugas, nullable]
User (1) ----< LogAktifitas (M)

Kendaraan (1) ----< Transaksi (M)
AreaParkir (1) ----< Transaksi (M)
Tarif (1) ----< Transaksi (M)

Transaksi (1) ---- Pembayaran (1)  [id_pembayaran, status_pembayaran]
```

---

## 12. Diagram Alur Bisnis

### Alur Check-in

1. Petugas membuka form "Catat Kendaraan Masuk".
2. Input plat nomor — sistem mengecek apakah kendaraan sudah terdaftar.
3. Jika terdaftar: data kendaraan terisi otomatis.
4. Jika belum terdaftar: isi form kendaraan baru.
5. Pilih tarif dan area parkir.
6. Sistem memvalidasi → DB transaction + lock area.
7. Cek kapasitas: `terisi < kapasitas`.
8. Buat transaksi, increment `terisi`.
9. Commit → redirect ke halaman Parkir Aktif.

### Alur Check-out

1. Petugas memilih transaksi dari daftar Parkir Aktif.
2. Konfirmasi checkout.
3. DB transaction + lock transaksi.
4. Hitung durasi & biaya otomatis.
5. Update status transaksi menjadi `keluar`, decrement `terisi`.
6. Redirect ke halaman Pilih Metode Pembayaran.

### Alur Pembayaran (Midtrans)

1. Sistem generate Snap Token dan `order_id` unik.
2. Frontend menampilkan modal pembayaran Midtrans.
3. Customer menyelesaikan pembayaran.
4. Midtrans mengirim webhook ke server.
5. Server memverifikasi status langsung ke API Midtrans.
6. Jika berhasil: buat record pembayaran, update status transaksi.
7. Fallback: jika webhook gagal, sinkronisasi dilakukan saat user membuka halaman success.

---

## 13. Lampiran: Entity-Relationship Diagram (ERD)

```
User (1) ----< Kendaraan (M)       [id_user nullable]
User (1) ----< Transaksi (M)       [id_user = operator]
User (1) ----< Pembayaran (M)      [id_user = petugas]
User (1) ----< LogAktifitas (M)

Kendaraan (1) ----< Transaksi (M)
AreaParkir (1) ----< Transaksi (M)
Tarif (1) ----< Transaksi (M)

Transaksi (1) ---- Pembayaran (1)  [id_pembayaran, status_pembayaran]
```

**Tabel utama:**

| Tabel | Deskripsi |
|-------|-----------|
| `tb_user` | Pengguna sistem (admin, petugas, owner, user) |
| `tb_kendaraan` | Data kendaraan (plat_nomor, jenis, warna, pemilik) |
| `tb_area_parkir` | Area parkir (nama, kapasitas, jumlah terisi) |
| `tb_tarif` | Tarif per jam berdasarkan jenis kendaraan |
| `tb_transaksi` | Transaksi parkir (check-in / check-out) |
| `tb_pembayaran` | Record pembayaran (hanya Midtrans) |
| `tb_log_aktivitas` | Audit trail aktivitas pengguna |

---

## 14. Lampiran: Lokasi File Penting

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