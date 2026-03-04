@extends('layouts.app')

@section('content')
    <div class="p-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">Halo Admin, berikut adalah ringkasan operasional parkir hari ini.</p>
            </div>
            <div class="flex gap-2">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-600 shadow-sm">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    Sistem Online: {{ now()->translatedFormat('d F Y') }}
                </span>
            </div>
        </div>

        <!-- Statistik Utama -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Revenue</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500 mt-1">Pendapatan Hari Ini</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Transactions</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $transaksiHariIni }}</p>
                <p class="text-xs text-gray-500 mt-1">Transaksi Hari Ini</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-amber-600 uppercase tracking-widest bg-amber-50 px-2 py-0.5 rounded-lg">Active</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $transaksiAktif }}</p>
                <p class="text-xs text-gray-500 mt-1">Kendaraan Sedang Parkir</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Users</span>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ $totalUser }}</p>
                <p class="text-xs text-gray-500 mt-1">Total Pengguna Terdaftar</p>
            </div>
        </div>

        <!-- Grafik & Visualisasi -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Tren Pendapatan</h2>
                        <p class="text-xs text-gray-500">7 Hari Terakhir</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-indigo-600">Total: Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="h-[300px]">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Distribusi Kendaraan</h2>
                <div class="h-[240px] flex items-center justify-center">
                    <canvas id="vehicleChart"></canvas>
                </div>
                <div class="mt-6 space-y-3">
                    @php 
                        $totalV = array_sum($grafikKendaraan['data']);
                    @endphp
                    @foreach($grafikKendaraan['labels'] as $index => $label)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full {{ $index == 0 ? 'bg-indigo-500' : 'bg-emerald-500' }}"></span>
                                <span class="text-gray-600">{{ $label }}</span>
                            </div>
                            <span class="font-bold text-gray-900">{{ $grafikKendaraan['data'][$index] }} ({{ $totalV > 0 ? round($grafikKendaraan['data'][$index] / $totalV * 100) : 0 }}%)</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Monitoring Area -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Status Area</h2>
                        <a href="{{ route('area-parkir.index') }}" class="text-[10px] font-bold text-indigo-600 hover:underline">Detail</a>
                    </div>
                    <div class="p-6 space-y-5">
                        @foreach($areaParkir as $area)
                            @php
                                $percent = $area->kapasitas > 0 ? ($area->terisi / $area->kapasitas * 100) : 0;
                            @endphp
                            <div>
                                <div class="flex justify-between items-center mb-1.5">
                                    <span class="text-xs font-bold text-gray-700">{{ $area->nama_area }}</span>
                                    <span class="text-[10px] font-bold {{ $percent >= 90 ? 'text-red-500' : 'text-emerald-500' }}">
                                        {{ $area->terisi }}/{{ $area->kapasitas }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5">
                                    <div class="{{ $percent >= 90 ? 'bg-red-500' : 'bg-indigo-500' }} h-1.5 rounded-full transition-all" style="width: {{ min($percent, 100) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-indigo-600 rounded-3xl p-6 text-white shadow-xl shadow-indigo-100 relative overflow-hidden group">
                    <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/10 rounded-full group-hover:scale-110 transition-transform"></div>
                    <h3 class="text-lg font-bold mb-2">Quick Actions</h3>
                    <div class="grid grid-cols-2 gap-3 mt-4 relative z-10">
                        <a href="{{ route('users.index') }}" class="p-3 bg-white/10 hover:bg-white/20 rounded-2xl flex flex-col items-center gap-2 transition-colors border border-white/10 text-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <span class="text-[10px] font-bold">Kelola User</span>
                        </a>
                        <a href="{{ route('parking-maps.index') }}" class="p-3 bg-white/10 hover:bg-white/20 rounded-2xl flex flex-col items-center gap-2 transition-colors border border-white/10 text-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h2l3 7 4-4 4 8 3-6h2"></path></svg>
                            <span class="text-[10px] font-bold">Edit Peta</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Aktivitas Terbaru -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl border border-gray-200 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Log Transaksi Terbaru</h2>
                        <a href="{{ route('transaksi.index') }}" class="text-[10px] font-bold text-indigo-600 hover:underline">Lihat Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <tbody class="divide-y divide-gray-50">
                                @forelse($aktivitasTerbaru as $trx)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-center font-bold text-gray-400 text-xs">
                                                    {{ substr($trx->kendaraan->plat_nomor ?? '-', 0, 2) }}
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-900">{{ $trx->kendaraan->plat_nomor ?? '-' }}</p>
                                                    <p class="text-[10px] text-gray-500 uppercase">{{ $trx->kendaraan->jenis_kendaraan ?? 'Kendaraan' }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                                <span class="text-xs text-gray-600">{{ $trx->area->nama_area ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <p class="text-xs font-bold text-gray-900">{{ $trx->waktu_masuk->diffForHumans() }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $trx->waktu_masuk->format('H:i') }} • {{ $trx->user->name ?? 'System' }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[10px] font-bold {{ $trx->status === 'masuk' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-700 border border-gray-100' }}">
                                                {{ ucfirst($trx->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-20 text-center">
                                            <p class="text-gray-400 text-sm italic">Belum ada aktivitas hari ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Revenue Chart
            const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
            new Chart(ctxRevenue, {
                type: 'line',
                data: {
                    labels: @json($grafikPendapatan['labels']),
                    datasets: [{
                        label: 'Pendapatan',
                        data: @json($grafikPendapatan['data']),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#6366f1',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            callbacks: {
                                label: function(context) {
                                    return ' Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [5, 5], color: '#f1f5f9' },
                            ticks: {
                                font: { size: 10 },
                                callback: function(value) {
                                    if (value >= 1000000) return (value / 1000000) + 'jt';
                                    if (value >= 1000) return (value / 1000) + 'rb';
                                    return value;
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 10 } }
                        }
                    }
                }
            });

            // Vehicle Chart
            const ctxVehicle = document.getElementById('vehicleChart').getContext('2d');
            new Chart(ctxVehicle, {
                type: 'doughnut',
                data: {
                    labels: @json($grafikKendaraan['labels']),
                    datasets: [{
                        data: @json($grafikKendaraan['data']),
                        backgroundColor: ['#6366f1', '#10b981'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
    </script>
@endpush
