# Struktur migrasi

File migrasi tidak lagi diletakkan langsung di folder ini agar lebih mudah dibaca.

| Folder | Isi |
|--------|-----|
| `framework/` | Migrasi bawaan Laravel (cache, jobs). |
| `app/` | Semua migrasi domain Neston (user, parkir, pembayaran, RFID, dll.). |

Urutan eksekusi tetap mengikuti **timestamp** di nama file (`YYYY_MM_DD_HHMMSS_...`), bukan nama folder.

## Migrasi baru

`php artisan make:migration nama` akan membuat file di folder ini (root). **Pindahkan** file tersebut ke `app/` (atau `framework/` jika memang untuk infrastruktur Laravel), lalu jalankan `php artisan migrate`.

Atau buat langsung di `app/` dengan opsi path:

```bash
php artisan make:migration nama_migration --path=database/migrations/app
```
