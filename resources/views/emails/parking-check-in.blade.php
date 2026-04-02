@php
    $userName = $transaksi->notifyTargetUser()?->name ?? 'Pengguna';
    $plat = $transaksi->kendaraan?->plat_nomor ?? '-';
    $masuk = $transaksi->waktu_masuk?->timezone(config('app.timezone'))?->format('d M Y, H:i') ?? '-';
    $lokasi = $transaksi->area?->nama_area ?? '-';
@endphp
<p>Halo <strong>{{ $userName }}</strong>,</p>
<p>Kendaraan Anda berhasil <strong>check-in</strong> di NESTON.</p>
<ul>
    <li><strong>Plat nomor:</strong> {{ $plat }}</li>
    <li><strong>Waktu masuk:</strong> {{ $masuk }}</li>
    <li><strong>Lokasi:</strong> {{ $lokasi }}</li>
</ul>
<p>Terima kasih telah menggunakan layanan kami.</p>
