@php
    $userName = $transaksi->notifyTargetUser()?->name ?? 'Pengguna';
    $plat = $transaksi->kendaraan?->plat_nomor ?? '-';
    $keluar = $transaksi->waktu_keluar?->timezone(config('app.timezone'))?->format('d M Y, H:i') ?? '-';
    $durasi = $transaksi->durasi_jam;
    $biaya = $transaksi->biaya_total;
@endphp
<p>Halo <strong>{{ $userName }}</strong>,</p>
<p>Kendaraan <strong>{{ $plat }}</strong> telah <strong>check-out</strong>.</p>
<ul>
    <li><strong>Waktu keluar:</strong> {{ $keluar }}</li>
    <li><strong>Durasi:</strong> {{ $durasi ?? '-' }} jam</li>
    <li><strong>Total biaya:</strong> Rp {{ isset($biaya) ? number_format((float) $biaya, 0, ',', '.') : '-' }}</li>
</ul>
<p>Silakan selesaikan pembayaran jika masih ada tagihan.</p>
<p>Terima kasih telah menggunakan layanan kami.</p>
