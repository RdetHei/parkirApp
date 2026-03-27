<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Parkir - #{{ str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=JetBrains+Mono:wght@400;700&family=Plus+Jakarta+Sans:wght@800&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f4f5;
        }

        .receipt-font {
            font-family: 'JetBrains Mono', monospace;
        }

        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }
            body {
                background-color: white !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 80mm;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .receipt-container {
                box-shadow: none !important;
                border: none !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 4mm !important;
                border-radius: 0 !important;
            }
            .receipt-container * {
                color: black !important;
                background-color: transparent !important;
                border-color: #000 !important;
                box-shadow: none !important;
            }
            .border-dashed {
                border-bottom: 1pt dashed #000 !important;
                border-top: none !important;
                border-left: none !important;
                border-right: none !important;
            }
            .bg-zinc-900 {
                background-color: #f4f4f5 !important; /* light gray for print visibility */
                border: 1pt solid #000 !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
            }
            .rounded-2xl, .rounded-3xl, .rounded-\[2\.5rem\], .rounded-\[2rem\], .rounded-xl {
                border-radius: 2mm !important;
                border: 0.5pt solid #eee !important;
            }
            .p-8 {
                padding: 4mm !important;
            }
            .p-6 {
                padding: 3mm !important;
            }
            .space-y-8 > :not([hidden]) ~ :not([hidden]) {
                margin-top: 5mm !important;
            }
            .space-y-4 > :not([hidden]) ~ :not([hidden]) {
                margin-top: 3mm !important;
            }
            .receipt-font {
                font-size: 12pt !important;
            }
            h1 {
                font-size: 18pt !important;
            }
            .text-2xl {
                font-size: 14pt !important;
            }
            .text-xl {
                font-size: 12pt !important;
            }
            .text-xs {
                font-size: 9pt !important;
            }
            .text-\[9px\], .text-\[8px\], .text-\[7px\], .text-\[10px\], .text-\[11px\] {
                font-size: 8pt !important;
                letter-spacing: 0.5pt !important;
                opacity: 1 !important;
            }
            .fa-qrcode {
                 color: #000 !important;
                 font-size: 40pt !important;
             }
             .animate-\[spin_20s_linear_infinite\] {
                 display: none !important;
             }
             .tracking-widest, .tracking-\[0\.3em\], .tracking-\[0\.4em\], .tracking-\[0\.2em\] {
                  letter-spacing: 0.5pt !important;
              }
               img {
                   filter: grayscale(100%) !important;
                   width: 10mm !important;
                   height: 10mm !important;
               }
               .bg-zinc-50, .bg-zinc-50\/50 {
                   background-color: #fafafa !important;
                   border: 0.5pt solid #eee !important;
               }
          }
    </style>
</head>
<body class="antialiased text-zinc-900 selection:bg-zinc-900 selection:text-white">

    <div class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-8">
        
        <!-- Receipt Card -->
        <div class="receipt-container w-full max-w-[400px] bg-white shadow-[0_20px_50px_rgba(0,0,0,0.1)] rounded-[2.5rem] overflow-hidden border border-zinc-100">
            
            <!-- Header Section -->
            <div class="p-8 text-center border-b border-dashed border-zinc-200">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-zinc-900 rounded-2xl mb-4">
                    <img src="{{ asset('images/neston.png') }}" alt="NESTON" class="w-8 h-8 invert brightness-200">
                </div>
                <h1 class="text-2xl font-[900] tracking-tighter uppercase leading-none">NESTON</h1>
                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-[0.3em] mt-2">Smart Parking AI</p>
                
                <div class="mt-6 pt-6 border-t border-zinc-100">
                    <p class="text-sm font-bold">{{ optional($transaksi->area)->nama_area ?? 'Lantai 1 - Utama' }}</p>
                    <p class="text-[10px] text-zinc-400 uppercase tracking-widest mt-1">Jakarta, Indonesia</p>
                </div>
            </div>

            <!-- Content Section -->
            <div class="p-8 space-y-8">
                
                <!-- Transaction Header -->
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">ID Transaksi</p>
                        <p class="receipt-font font-bold text-base">#{{ str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="text-right space-y-1">
                        <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Tanggal</p>
                        <p class="text-xs font-bold">{{ now()->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <!-- Vehicle Info -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center bg-zinc-50 p-4 rounded-2xl border border-zinc-100">
                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">Plat Nomor</span>
                        <span class="receipt-font font-black text-xl tracking-tighter">{{ $transaksi->kendaraan->plat_nomor ?? '-' }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-zinc-50/50 p-3 rounded-xl border border-zinc-100/50">
                            <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Jenis</p>
                            <p class="text-[11px] font-bold uppercase truncate">{{ $transaksi->kendaraan->jenis_kendaraan ?? '-' }}</p>
                        </div>
                        <div class="bg-zinc-50/50 p-3 rounded-xl border border-zinc-100/50">
                            <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-widest mb-1">Warna</p>
                            <p class="text-[11px] font-bold uppercase truncate">{{ $transaksi->kendaraan->warna ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Time Details -->
                <div class="space-y-3 border-y border-dashed border-zinc-200 py-6">
                    <div class="flex justify-between text-xs">
                        <span class="text-zinc-500 font-medium uppercase tracking-wider">Waktu Masuk</span>
                        <span class="font-bold text-zinc-800">{{ $transaksi->waktu_masuk ? \Carbon\Carbon::parse($transaksi->waktu_masuk)->format('H:i:s d/m/y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-zinc-500 font-medium uppercase tracking-wider">Waktu Keluar</span>
                        <span class="font-bold text-zinc-800">
                            @if($transaksi->waktu_keluar)
                                {{ \Carbon\Carbon::parse($transaksi->waktu_keluar)->format('H:i:s d/m/y') }}
                            @else
                                <span class="text-zinc-400 italic">BELUM KELUAR</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-xs pt-2 border-t border-zinc-50">
                        <span class="text-zinc-500 font-medium uppercase tracking-wider">Durasi</span>
                        <span class="font-bold text-zinc-800">{{ $transaksi->durasi_jam ?? '0' }} JAM</span>
                    </div>
                </div>

                <!-- Cost Calculation -->
                <div class="space-y-4">
                    <div class="flex justify-between items-center px-1">
                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Tarif Per Jam</span>
                        <span class="text-xs font-bold text-zinc-600">Rp {{ number_format($transaksi->tarif->tarif_perjam ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="bg-zinc-900 text-white p-6 rounded-[2rem] shadow-xl relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/5 rounded-full -mr-12 -mt-12 blur-2xl"></div>
                        <div class="relative z-10 flex justify-between items-center">
                            <span class="text-[10px] font-bold tracking-[0.2em] uppercase opacity-60">Total Bayar</span>
                            <span class="text-2xl font-black tracking-tighter">Rp {{ number_format($transaksi->biaya_total ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- QR & Validation -->
                <div class="pt-4 flex flex-col items-center justify-center space-y-3">
                    <div class="w-32 h-32 bg-zinc-50 rounded-3xl border border-zinc-100 flex items-center justify-center relative group">
                        <i class="fa-solid fa-qrcode text-5xl text-zinc-200 group-hover:text-zinc-400 transition-colors"></i>
                        <div class="absolute inset-0 border-2 border-dashed border-zinc-200 rounded-3xl animate-[spin_20s_linear_infinite] opacity-50"></div>
                    </div>
                    <div class="text-center">
                        <p class="text-[8px] font-bold text-zinc-400 uppercase tracking-[0.4em]">Verified by Neston</p>
                        <p class="text-[7px] text-zinc-300 uppercase tracking-widest mt-1">S/N: {{ strtoupper(substr(md5($transaksi->id_parkir), 0, 12)) }}</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-zinc-50 p-8 text-center border-t border-zinc-100">
                <p class="text-[11px] font-bold text-zinc-800 uppercase tracking-widest">Terima Kasih</p>
                <p class="text-[10px] text-zinc-400 leading-relaxed mt-2 px-4">
                    Struk ini adalah bukti pembayaran yang sah. Harap simpan dengan baik.
                </p>
                <div class="mt-6 flex items-center justify-center gap-2">
                    <span class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">Operator:</span>
                    <span class="text-[9px] font-bold text-zinc-900 uppercase tracking-widest">{{ explode(' ', $transaksi->user->name ?? 'SYSTEM')[0] }}</span>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="no-print w-full max-w-[400px] mt-8 flex flex-col sm:flex-row gap-4">
            <button onclick="window.print()" class="flex-1 bg-white text-black font-bold py-4 rounded-2xl shadow-xl hover:bg-zinc-50 transition-all active:scale-95 flex items-center justify-center gap-3 border border-zinc-200">
                <i class="fa-solid fa-print"></i>
                Cetak Struk
            </button>
            <a href="{{ route('transaksi.parkir.index') }}" class="flex-1 bg-zinc-900 text-white font-bold py-4 rounded-2xl shadow-xl hover:bg-zinc-800 transition-all active:scale-95 flex items-center justify-center gap-3">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>

        <p class="no-print mt-8 text-[10px] font-bold text-zinc-400 uppercase tracking-[0.3em]">© 2026 Neston Core</p>
    </div>

</body>
</html>
