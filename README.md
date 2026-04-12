# NESTON — Smart Parking System

**NESTON** adalah sistem manajemen parkir pintar berbasis Laravel yang mengintegrasikan pengenalan plat nomor otomatis (ANPR), akses RFID, dan sistem pembayaran multi-kanal (Midtrans & Tunai).

## 🚀 Fitur Utama
- **ANPR (Automatic Plate Number Recognition)**: Integrasi dengan *Plate Recognizer* untuk pemindaian plat nomor otomatis.
- **RFID Access Control**: Mendukung autentikasi dan pencatatan transaksi menggunakan kartu RFID.
- **Multi-Channel Payment**: 
  - Pembayaran otomatis melalui **Midtrans** (Gopay, ShopeePay, VA, dll).
  - Pembayaran tunai melalui sistem kasir petugas.
  - Sistem saldo **NestonPay** untuk kemudahan transaksi internal.
- **WhatsApp Gateway**: Notifikasi check-in, check-out, dan tagihan via **Fonnte** atau **UltraMsg**.
- **Real-time Parking Map**: Visualisasi slot parkir (kosong, terisi, dibookmark) menggunakan Leaflet.js.
- **Multi-Role Dashboards**: Dashboard khusus untuk Admin, Owner, Petugas, dan User.

## 🛠️ Stack Teknologi
- **Framework**: Laravel 12 (PHP 8.2+)
- **Database**: MySQL / MariaDB
- **Frontend**: Blade, Tailwind CSS, Alpine.js, Leaflet.js
- **Services**: 
  - Midtrans API (Payment Gateway)
  - Plate Recognizer API (ANPR)
  - Cloudinary (Image Hosting)
  - Fonnte / UltraMsg (WhatsApp Gateway)

## 📋 Prasyarat Instalasi
1. PHP >= 8.2
2. Composer
3. Node.js & NPM
4. Laragon / XAMPP / Docker

## ⚙️ Instalasi
1. Clone repositori:
   ```bash
   git clone <repository-url>
   cd neston
   ```
2. Instal dependensi PHP:
   ```bash
   composer install
   ```
3. Instal dependensi Frontend:
   ```bash
   npm install && npm run build
   ```
4. Konfigurasi Environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
5. Migrasi & Seed Database:
   ```bash
   php artisan migrate
   php artisan db:seed --class=AreaParkirSeeder
   php artisan db:seed --class=TarifSeeder
   php artisan db:seed --class=KasirDemoSeeder
   ```

## 🔑 Konfigurasi .env (Penting)
Pastikan Anda mengisi kredensial berikut di file `.env`:

```env
# Midtrans
MIDTRANS_SERVER_KEY=your_server_key
MIDTRANS_CLIENT_KEY=your_client_key
MIDTRANS_IS_PRODUCTION=false

# Plate Recognizer (ANPR)
PLATE_RECOGNIZER_KEY=your_api_key

# WhatsApp Gateway
WHATSAPP_ENABLED=true
WHATSAPP_DRIVER=fonnte # fonnte | ultramsg
WHATSAPP_GATEWAY_URL=https://api.fonnte.com/send
WHATSAPP_API_TOKEN=your_token

# Cloudinary
CLOUDINARY_URL=your_cloudinary_url
```

## 📂 Arsitektur Kode
- **Models**: [App\Models](file:///c:/laragon/www/neston/app/Models) (Transaksi, User, Kendaraan, AreaParkir, dll).
- **Controllers**: [App\Http\Controllers](file:///c:/laragon/www/neston/app/Http/Controllers) (Logika bisnis per fitur).
- **Services**: 
  - [WhatsAppGateway.php](file:///c:/laragon/www/neston/app/Services/WhatsAppGateway.php): Penanganan notifikasi WA.
  - [PlateRecognizerService.php](file:///c:/laragon/www/neston/app/Services/PlateRecognizerService.php): Integrasi API ANPR.
- **Middleware**: 
  - [RoleMiddleware.php](file:///c:/laragon/www/neston/app/Http/Middleware/RoleMiddleware.php): Hak akses user.
  - [NoCacheMiddleware.php](file:///c:/laragon/www/neston/app/Http/Middleware/NoCacheMiddleware.php): Keamanan sesi pasca-logout.

## 👤 Akun Demo
Jalankan `php artisan db:seed --class=KasirDemoSeeder` untuk mendapatkan akun berikut:
- **Owner**: `owner@gmail.com` | `password`
- **Admin**: `admin@gmail.com` | `password`
- **Petugas**: `petugas@gmail.com` | `password`
- **User**: `user@gmail.com` | `password`

---
© 2026 NESTON Smart Parking System.


