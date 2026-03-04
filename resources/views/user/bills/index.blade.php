@extends('layouts.app')

@section('title', 'Tagihan Saya')

@section('content')
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="max-w-5xl mx-auto space-y-6">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold tracking-wide text-emerald-600 uppercase">Tagihan parkir</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                        Tagihan milik {{ $user->name }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Daftar transaksi parkir yang sudah checkout tetapi belum dibayar.
                    </p>
                </div>
                <a href="{{ route('user.dashboard') }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Kembali ke dashboard
                </a>
            </div>

            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-gray-200 p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-medium text-gray-600">Jumlah tagihan aktif</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">
                        {{ $transaksis->total() }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-medium text-gray-600">Perkiraan total yang harus dibayar</p>
                    <p class="mt-1 text-xl font-bold text-emerald-600">
                        Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-900">Daftar tagihan</h2>
                </div>
                <div class="p-4">
                    @if($transaksis->count())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Plat</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Area</th>
                                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wide">Waktu keluar</th>
                                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Total</th>
                                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-600 uppercase tracking-wide">Aksi</th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                @foreach($transaksis as $trx)
                                    <tr>
                                        <td class="px-4 py-2 font-semibold text-gray-900">
                                            {{ $trx->kendaraan->plat_nomor ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-700">
                                            {{ $trx->area->nama_area ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-700">
                                            {{ optional($trx->waktu_keluar)->format('d M Y, H:i') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2 text-right font-semibold text-gray-900">
                                            Rp {{ number_format($trx->biaya_total ?? 0, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <a href="{{ route('payment.midtrans', $trx->id_parkir) }}"
                                               class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700">
                                                Bayar sekarang
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $transaksis->links() }}
                        </div>
                    @else
                        <p class="text-sm text-gray-500">
                            Belum ada transaksi yang menunggu pembayaran.
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

