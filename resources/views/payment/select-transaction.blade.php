@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Pilih Transaksi untuk Pembayaran</h1>

        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID Parkir</th>
                                <th>Plat Nomor</th>
                                <th>Waktu Masuk</th>
                                <th>Jenis Kendaraan</th>
                                <th>Area Parkir</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksis as $transaksi)
                                <tr>
                                    <td>{{ $transaksi->id_parkir }}</td>
                                    <td>{{ $transaksi->kendaraan->plat_nomor }}</td>
                                    <td>{{ $transaksi->waktu_masuk }}</td>
                                    <td>{{ $transaksi->tarif->jenis_kendaraan }}</td>
                                    <td>{{ $transaksi->area->nama_area }}</td>
                                    <td>
                                        <a href="{{ route('payment.manual-confirm', $transaksi->id_parkir) }}" class="btn btn-primary btn-sm">Bayar Manual</a>
                                        <a href="{{ route('payment.qr-scan', $transaksi->id_parkir) }}" class="btn btn-success btn-sm">Bayar QR</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada transaksi parkir yang aktif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
