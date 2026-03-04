@extends('layouts.app')

@section('content')
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto space-y-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Selamat datang</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                        {{ $user->name }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Ringkasan aktivitas parkir pribadi Anda.
                    </p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('user.vehicles.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                        </svg>
                        Kendaraan saya
                    </a>
                    <a href="{{ route('user.bookings') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-medium text-emerald-800 shadow-sm hover:bg-emerald-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Booking slot
                    </a>
                    <a href="{{ route('user.bills') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800 shadow-sm hover:bg-amber-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M7 7h10a2 2 0 012 2v6a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2z"></path>
                        </svg>
                        Tagihan saya
                    </a>
                    <a href="{{ route('user.profile') }}"
                       class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-medium text-blue-800 shadow-sm hover:bg-blue-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Edit Profil
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-4 border border-gray-200">
                    <p class="text-xs font-medium text-gray-500">Kendaraan saya</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $totalKendaraan }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-200">
                    <p class="text-xs font-medium text-gray-500">Total transaksi</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $totalTransaksi }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-200">
                    <p class="text-xs font-medium text-gray-500">Parkir aktif</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-600">{{ $transaksiAktif }}</p>
                </div>
                <div class="bg-white rounded-2xl p-4 border border-gray-200">
                    <p class="text-xs font-medium text-gray-500">Tagihan belum dibayar</p>
                    <p class="mt-2 text-2xl font-bold text-amber-600">{{ $transaksiBelumDibayar }}</p>
                    <p class="mt-1 text-[11px] text-gray-500">Segera selesaikan pembayaran untuk menghindari masalah di loket keluar.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Parkir aktif saya</h2>
                            <p class="text-xs text-gray-500">Kendaraan yang saat ini masih tercatat parkir.</p>
                        </div>
                    </div>
                    <div class="p-5">
                        @php
                            $activeParkings = $riwayatTransaksi->where('status', 'masuk');
                        @endphp
                        @if($transaksiAktif > 0 && $activeParkings->count())
                            <ul class="divide-y divide-gray-100">
                                @foreach($activeParkings as $trx)
                                    @php
                                        $masuk = \Carbon\Carbon::parse($trx->waktu_masuk);
                                        $durasiMenit = now()->diffInMinutes($masuk);
                                        $durasiJam = ceil($durasiMenit / 60);
                                        $estimasiBiaya = $durasiJam * ($trx->tarif->tarif_perjam ?? 0);
                                    @endphp
                                    <li class="py-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <p class="text-sm font-bold text-gray-900">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                                    <p class="text-[10px] text-gray-500 uppercase">{{ $trx->area->nama_area ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-bold text-emerald-600">Rp {{ number_format($estimasiBiaya, 0, ',', '.') }}</p>
                                                <p class="text-[10px] text-gray-400">Estimasi biaya</p>
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-4 mt-3 bg-gray-50 rounded-xl p-3 border border-gray-100">
                                            <div>
                                                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-medium">Waktu Masuk</p>
                                                <p class="text-xs font-semibold text-gray-700">{{ $masuk->format('H:i') }} <span class="text-[10px] font-normal text-gray-400">({{ $masuk->format('d/m/Y') }})</span></p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-medium">Durasi</p>
                                                <p class="text-xs font-semibold text-gray-700">
                                                    {{ floor($durasiMenit / 60) }}j {{ $durasiMenit % 60 }}m
                                                </p>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="py-8 text-center">
                                <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-500">Tidak ada parkir aktif saat ini.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-sm font-semibold text-gray-900">Riwayat parkir terakhir</h2>
                            <p class="text-xs text-gray-500">5 transaksi parkir terakhir atas nama Anda.</p>
                        </div>
                        <a href="{{ route('user.history') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">Lihat Semua</a>
                    </div>
                    <div class="p-5">
                        @if($riwayatTransaksi->count())
                            <ul class="divide-y divide-gray-100">
                                @foreach($riwayatTransaksi as $trx)
                                    <li class="py-3 flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $trx->kendaraan->plat_nomor ?? '-' }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($trx->waktu_masuk)->format('d M Y, H:i') }}
                                                @if($trx->waktu_keluar)
                                                    — {{ \Carbon\Carbon::parse($trx->waktu_keluar)->format('H:i') }}
                                                @endif
                                                • Area: {{ $trx->area->nama_area ?? '-' }}
                                            </p>
                                        </div>
                                        @if($trx->biaya_total)
                                            <p class="text-xs font-semibold text-gray-900">
                                                Rp {{ number_format($trx->biaya_total, 0, ',', '.') }}
                                            </p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-sm text-gray-500">Belum ada riwayat transaksi parkir.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-semibold text-gray-900">Riwayat pembayaran saya</h2>
                        <p class="text-xs text-gray-500">5 pembayaran terakhir yang tercatat dengan akun Anda.</p>
                    </div>
                </div>
                <div class="p-5">
                    @if($riwayatPembayaran->count())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Waktu</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Metode</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Nominal</th>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($riwayatPembayaran as $pay)
                                        <tr>
                                            <td class="px-4 py-2">
                                                <div class="text-xs text-gray-900">
                                                    {{ optional($pay->waktu_pembayaran)->format('d M Y') ?? '-' }}
                                                </div>
                                                <div class="text-[11px] text-gray-500">
                                                    {{ optional($pay->waktu_pembayaran)->format('H:i') ?? '' }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 text-xs text-gray-700">
                                                {{ $pay->metode ?? '-' }}
                                            </td>
                                            <td class="px-4 py-2 text-xs font-semibold text-gray-900">
                                                Rp {{ number_format($pay->nominal, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2">
                                                @php
                                                    $status = $pay->status ?? 'pending';
                                                    $badge = $status === 'berhasil'
                                                        ? 'bg-emerald-100 text-emerald-800'
                                                        : ($status === 'gagal'
                                                            ? 'bg-red-100 text-red-800'
                                                            : 'bg-yellow-100 text-yellow-800');
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium {{ $badge }}">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Belum ada pembayaran yang tercatat untuk akun ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
