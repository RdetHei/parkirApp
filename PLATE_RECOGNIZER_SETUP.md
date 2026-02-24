# Plate Recognizer Integration Setup

## Konfigurasi

### 1. Tambahkan API Key ke `.env`

Tambahkan kunci berikut ke file `.env` Anda:

```env
PLATE_RECOGNIZER_KEY=your_api_key_here
```

**Cara mendapatkan API Key:**
1. Daftar di [Plate Recognizer](https://platerecognizer.com/)
2. Buat akun dan pilih paket yang sesuai
3. Salin API key dari dashboard
4. Tambahkan ke file `.env`

### 2. Update Konfigurasi Services

Konfigurasi sudah ditambahkan di `config/services.php`:

```php
'plate_recognizer' => [
    'key' => env('PLATE_RECOGNIZER_KEY', ''),
],
```

## Penggunaan

### Backend API

Endpoint tersedia di: `POST /scan-plate`

**Request:**
- Method: `POST`
- Content-Type: `multipart/form-data`
- Body:
  - `image` (file): Gambar plat nomor (max 5MB, format: JPG/PNG)
  - `debug` (optional, boolean): Include raw response dari API

**Response Success:**
```json
{
    "success": true,
    "plate_number": "B1234XYZ",
    "confidence": 0.95,
    "valid": true,
    "message": "Plat nomor berhasil dideteksi",
    "raw_response": null
}
```

**Response Error:**
```json
{
    "success": false,
    "message": "Error message",
    "plate_number": null,
    "confidence": 0,
    "valid": false
}
```

### Frontend Component

Komponen kamera sudah terintegrasi di halaman check-in (`/transaksi/create-check-in`).

**Fitur:**
- Akses kamera belakang (environment camera) untuk mobile
- Capture gambar
- Upload dan scan otomatis
- Auto-fill ke select kendaraan jika plat terdeteksi
- Confidence threshold: 80% minimum
- Loading indicator
- Error handling lengkap

**Cara menggunakan komponen di view lain:**

```blade
<x-plate-scanner 
    target-input-id="id_kendaraan" 
    target-input-type="select"
    :on-scan-success="'callbackFunctionName'"
/>
```

**Parameter:**
- `target-input-id`: ID dari input/select yang akan diisi otomatis
- `target-input-type`: `'select'` atau `'text'`
- `on-scan-success`: (optional) Nama fungsi JavaScript callback

## Spesifikasi

- **API Endpoint**: `https://api.platerecognizer.com/v1/plate-reader/`
- **Max File Size**: 5MB
- **Supported Formats**: JPG, JPEG, PNG
- **Confidence Threshold**: 80%
- **Camera**: Menggunakan `facingMode: 'environment'` (kamera belakang)

## Error Handling

Sistem menangani berbagai error:
- API key tidak dikonfigurasi
- API request gagal
- Tidak ada plat terdeteksi
- Confidence di bawah threshold
- File tidak valid
- Kamera tidak dapat diakses

## Testing

1. Pastikan `PLATE_RECOGNIZER_KEY` sudah di-set di `.env`
2. Buka halaman check-in: `/transaksi/create-check-in`
3. Klik "Buka Kamera"
4. Ambil foto plat nomor
5. Klik "Scan Plat"
6. Hasil akan otomatis mengisi select kendaraan jika valid

## Troubleshooting

**Kamera tidak bisa diakses:**
- Pastikan browser mendukung getUserMedia
- Berikan izin kamera di browser
- Gunakan HTTPS (required untuk getUserMedia di production)

**API Error:**
- Cek API key di `.env`
- Pastikan koneksi internet stabil
- Cek quota API di dashboard Plate Recognizer

**Plat tidak terdeteksi:**
- Pastikan gambar jelas dan fokus
- Pastikan plat nomor terlihat jelas
- Coba dengan pencahayaan yang lebih baik