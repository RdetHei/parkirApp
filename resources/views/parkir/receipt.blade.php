<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Parkir - #{{ str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;500;600;700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --ink: #111111;
            --ink-mid: #444444;
            --ink-light: #888888;
            --ink-faint: #cccccc;
            --paper: #ffffff;
            --paper-off: #f8f7f4;
            --accent: #111111;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'IBM Plex Sans', sans-serif;
            background: #e8e6e1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            color: var(--ink);
        }

        .mono { font-family: 'IBM Plex Mono', monospace; }

        /* ── Receipt shell ── */
        .receipt {
            width: 100%;
            max-width: 360px;
            background: var(--paper);
            position: relative;
            border-radius: 4px;
            box-shadow:
                0 1px 1px rgba(0,0,0,0.04),
                0 4px 12px rgba(0,0,0,0.08),
                0 20px 48px rgba(0,0,0,0.10);
        }

        /* Torn-paper top edge */
        .receipt::before {
            content: '';
            display: block;
            height: 10px;
            background:
                radial-gradient(circle at 50% 0%, var(--paper) 6px, transparent 6px),
                repeating-linear-gradient(90deg, transparent 0px, transparent 10px, var(--paper) 10px, var(--paper) 14px);
            background-color: #e8e6e1;
            background-size: 14px 10px, 14px 10px;
        }

        /* Torn-paper bottom edge */
        .receipt::after {
            content: '';
            display: block;
            height: 10px;
            background:
                radial-gradient(circle at 50% 100%, var(--paper) 6px, transparent 6px),
                repeating-linear-gradient(90deg, transparent 0px, transparent 10px, var(--paper) 10px, var(--paper) 14px);
            background-color: #e8e6e1;
            background-size: 14px 10px, 14px 10px;
            transform: rotate(180deg);
        }

        .receipt-body { padding: 0 2rem 1rem; }

        /* ── Sections ── */
        .section { padding: 1.25rem 0; }
        .section + .section { border-top: 1px dashed var(--ink-faint); }

        /* ── Header ── */
        .brand-logo {
            width: 36px; height: 36px;
            background: var(--ink);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 0.75rem;
        }
        .brand-logo img { width: 22px; }

        .brand-name {
            font-family: 'IBM Plex Mono', monospace;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.3em;
            text-transform: uppercase;
            color: var(--ink);
        }

        .brand-sub {
            font-size: 0.6rem;
            font-weight: 600;
            letter-spacing: 0.25em;
            text-transform: uppercase;
            color: var(--ink-light);
            margin-top: 2px;
        }

        /* ── Row ── */
        .row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 0.5rem;
            padding: 0.3rem 0;
        }
        .row-label {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--ink-light);
            white-space: nowrap;
            flex-shrink: 0;
        }
        .row-dots {
            flex: 1;
            border-bottom: 1px dotted var(--ink-faint);
            margin: 0 0.4rem;
            margin-bottom: 3px;
        }
        .row-value {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--ink);
            text-align: right;
            white-space: nowrap;
        }

        /* ── Plate highlight ── */
        .plate-box {
            border: 2px solid var(--ink);
            border-radius: 6px;
            padding: 0.6rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--paper-off);
            margin: 0.5rem 0;
        }
        .plate-label {
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--ink-light);
        }
        .plate-number {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 1.35rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            color: var(--ink);
        }

        /* ── Total box ── */
        .total-box {
            background: var(--ink);
            color: white;
            border-radius: 6px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.75rem;
        }
        .total-label {
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            opacity: 0.6;
        }
        .total-amount {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        /* ── Status badge ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border: 1.5px solid var(--ink);
            border-radius: 100px;
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: var(--ink);
        }
        .status-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--ink);
        }

        /* ── Barcode-style ── */
        .barcode-area {
            text-align: center;
            padding: 0.75rem 0;
        }
        .barcode-lines {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 2px;
            height: 40px;
            margin-bottom: 0.5rem;
        }
        .barcode-lines span {
            background: var(--ink);
            border-radius: 1px;
        }
        .serial {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.6rem;
            letter-spacing: 0.2em;
            color: var(--ink-light);
        }

        /* ── Footer ── */
        .footer-text {
            font-size: 0.6rem;
            color: var(--ink-light);
            text-align: center;
            line-height: 1.7;
            letter-spacing: 0.03em;
        }
        .footer-tagline {
            font-family: 'IBM Plex Mono', monospace;
            font-size: 0.55rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--ink-faint);
            text-align: center;
            margin-top: 0.5rem;
        }

        /* ── Action buttons (screen only) ── */
        .actions {
            width: 100%;
            max-width: 360px;
            margin-top: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.85rem 1.5rem;
            border-radius: 8px;
            font-family: 'IBM Plex Sans', sans-serif;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.15s;
            text-decoration: none;
            border: none;
        }
        .btn:active { transform: scale(0.97); }
        .btn-primary {
            background: var(--ink);
            color: white;
        }
        .btn-primary:hover { background: #333; }
        .btn-secondary {
            background: white;
            color: var(--ink);
            border: 1.5px solid var(--ink-faint);
        }
        .btn-secondary:hover { border-color: var(--ink); }

        .copy { font-size: 0.6rem; color: var(--ink-light); text-align: center; margin-top: 1rem; letter-spacing: 0.1em; }

        /* ═══════════════════════════════
           PRINT STYLES
        ═══════════════════════════════ */
        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }

            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .actions, .copy { display: none !important; }

            .receipt {
                max-width: 100% !important;
                width: 80mm !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .receipt::before,
            .receipt::after { display: none !important; }

            .receipt-body { padding: 0 5mm 3mm !important; }

            .section { padding: 3mm 0 !important; }

            .brand-logo { width: 8mm !important; height: 8mm !important; margin-bottom: 2mm !important; }
            .brand-logo img { width: 5mm !important; }
            .brand-name { font-size: 12pt !important; }
            .brand-sub { font-size: 6pt !important; }

            .plate-number { font-size: 14pt !important; }
            .plate-box { padding: 2mm 3mm !important; margin: 1mm 0 !important; }

            .row { padding: 0.5mm 0 !important; }
            .row-label { font-size: 6pt !important; }
            .row-value { font-size: 7pt !important; }

            .total-box { padding: 2.5mm 3mm !important; margin-top: 2mm !important; border-radius: 3px !important; }
            .total-label { font-size: 6pt !important; }
            .total-amount { font-size: 12pt !important; }

            .barcode-lines { height: 10mm !important; margin-bottom: 1.5mm !important; }
            .serial { font-size: 6pt !important; }

            .footer-text { font-size: 6pt !important; }
            .footer-tagline { font-size: 5pt !important; margin-top: 1mm !important; }

            .status-badge { font-size: 5.5pt !important; padding: 0.5mm 1.5mm !important; }
        }
    </style>
</head>
<body>

    <div class="receipt">
        <div class="receipt-body">

            {{-- ── HEADER ── --}}
            <div class="section" style="text-align:center; padding-top: 1.5rem;">
                <div class="brand-logo">
                    <img src="{{ asset('images/neston.svg') }}" alt="NESTON">
                </div>
                <div class="brand-name">NESTON</div>
                <div class="brand-sub">Smart Parking System</div>

                <div style="margin-top: 1rem; display: flex; align-items: center; justify-content: space-between;">
                    <div style="text-align:left;">
                        <div class="row-label">Area</div>
                        <div style="font-size:0.75rem; font-weight:600; margin-top:2px;">
                            {{ optional($transaksi->area)->nama_area ?? 'Area Utama' }}
                        </div>
                        @if($transaksi->parkingMapSlot)
                            <div class="row-label" style="margin-top: 8px;">Slot</div>
                            <div style="font-size:1.1rem; font-weight:800; color:#111; font-family:'IBM Plex Mono';">
                                {{ $transaksi->parkingMapSlot->code }}
                            </div>
                        @endif
                    </div>
                    <div style="text-align:right;">
                        <div class="row-label">No. Transaksi</div>
                        <div class="mono" style="font-size:0.8rem; font-weight:700; margin-top:2px;">
                            #{{ str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── KENDARAAN ── --}}
            <div class="section">
                <div class="plate-box">
                    <div>
                        <div class="plate-label">Plat Nomor</div>
                        <div class="plate-number">{{ $transaksi->kendaraan->plat_nomor ?? '—' }}</div>
                    </div>
                    <div style="text-align:right;">
                        <div class="row-label" style="margin-bottom:2px;">Jenis</div>
                        <div style="font-size:0.7rem; font-weight:700; text-transform:uppercase;">
                            {{ $transaksi->kendaraan->jenis_kendaraan ?? '—' }}
                        </div>
                        @if(!empty($transaksi->kendaraan->warna))
                        <div style="font-size:0.65rem; color:#888; font-weight:600; text-transform:uppercase; margin-top:2px;">
                            {{ $transaksi->kendaraan->warna }}
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Status badge --}}
                <div style="margin-top: 0.6rem; display:flex; align-items:center; justify-content:space-between;">
                    @if($transaksi->waktu_keluar)
                        <div class="status-badge">
                            <span class="status-dot"></span>
                            Selesai
                        </div>
                    @else
                        <div class="status-badge" style="border-color:#999; color:#999;">
                            <span class="status-dot" style="background:#999;"></span>
                            Masih Parkir
                        </div>
                    @endif
                    <div style="font-size:0.6rem; color:#888; font-weight:600; letter-spacing:0.08em;">
                        {{ now()->format('d/m/Y · H:i') }}
                    </div>
                </div>
            </div>

            {{-- ── WAKTU ── --}}
            <div class="section">
                <div class="row">
                    <span class="row-label">Waktu Masuk</span>
                    <span class="row-dots"></span>
                    <span class="row-value mono">
                        {{ $transaksi->waktu_masuk ? \Carbon\Carbon::parse($transaksi->waktu_masuk)->format('H:i  d/m/y') : '—' }}
                    </span>
                </div>
                <div class="row">
                    <span class="row-label">Waktu Keluar</span>
                    <span class="row-dots"></span>
                    <span class="row-value mono" style="{{ !$transaksi->waktu_keluar ? 'color:#aaa; font-style:italic;' : '' }}">
                        @if($transaksi->waktu_keluar)
                            {{ \Carbon\Carbon::parse($transaksi->waktu_keluar)->format('H:i  d/m/y') }}
                        @else
                            Belum keluar
                        @endif
                    </span>
                </div>
                <div class="row" style="margin-top:0.25rem; padding-top:0.5rem; border-top: 1px solid #eee;">
                    <span class="row-label">Total Durasi</span>
                    <span class="row-dots"></span>
                    <span class="row-value mono" style="font-size:0.85rem; font-weight:700;">
                        {{ $transaksi->durasi_jam ?? '0' }} Jam
                    </span>
                </div>
            </div>

            {{-- ── BIAYA ── --}}
            <div class="section">
                <div class="row">
                    <span class="row-label">Tarif / Jam</span>
                    <span class="row-dots"></span>
                    <span class="row-value">Rp {{ number_format($transaksi->tarif->tarif_perjam ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="row">
                    <span class="row-label">Durasi</span>
                    <span class="row-dots"></span>
                    <span class="row-value">{{ $transaksi->durasi_jam ?? 0 }} × tarif</span>
                </div>

                @if($transaksi->diskon > 0)
                <div class="row" style="color: #059669;">
                    <span class="row-label" style="color: #059669;">Diskon Member</span>
                    <span class="row-dots" style="border-color: #a7f3d0;"></span>
                    <span class="row-value mono">-Rp {{ number_format($transaksi->diskon, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="total-box">
                    <div>
                        <div class="total-label">Total Bayar</div>
                        <div style="font-size:0.55rem; color:rgba(255,255,255,0.45); margin-top:1px; font-weight:600; letter-spacing:0.1em; text-transform:uppercase;">
                            Sudah termasuk pajak
                        </div>
                    </div>
                    <div class="total-amount">
                        Rp {{ number_format($transaksi->biaya_total ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            {{-- ── BARCODE / SERIAL ── --}}
            <div class="section" style="padding: 0.75rem 0;">
                <div class="barcode-area">
                    {{-- Pseudo-barcode dari hash ID --}}
                    @php
                        $hash = md5($transaksi->id_parkir);
                        $bars = [];
                        for ($i = 0; $i < 38; $i++) {
                            $v = hexdec($hash[$i % strlen($hash)]);
                            $bars[] = ['w' => ($v % 2 === 0) ? 2 : 1, 'h' => 20 + ($v * 1.2)];
                        }
                    @endphp
                    <div class="barcode-lines">
                        @foreach($bars as $bar)
                            <span style="width:{{ $bar['w'] }}px; height:{{ $bar['h'] }}px;"></span>
                        @endforeach
                    </div>
                    <div class="serial">{{ strtoupper(substr(md5($transaksi->id_parkir), 0, 4)) }} {{ strtoupper(substr(md5($transaksi->id_parkir), 4, 4)) }} {{ strtoupper(substr(md5($transaksi->id_parkir), 8, 4)) }} {{ strtoupper(substr(md5($transaksi->id_parkir), 12, 4)) }}</div>
                </div>
            </div>

            {{-- ── FOOTER ── --}}
            <div class="section" style="padding-bottom: 1rem;">
                <div class="footer-text">
                    Struk ini merupakan bukti pembayaran yang sah.<br>
                    Harap simpan struk sampai keluar area parkir.<br>
                    <span style="margin-top:4px; display:inline-block;">
                        Operator: <strong style="color:#333;">{{ explode(' ', $transaksi->user->name ?? 'SYSTEM')[0] }}</strong>
                        &nbsp;·&nbsp;
                        {{ optional($transaksi->area)->nama_area ?? 'Area Utama' }}
                    </span>
                </div>
                <div class="footer-tagline" style="margin-top: 0.75rem;">
                    — NESTON SMART PARKING · {{ date('Y') }} —
                </div>
            </div>

        </div>{{-- /receipt-body --}}
    </div>{{-- /receipt --}}

    {{-- ── ACTION BUTTONS (screen only) ── --}}
    <div class="actions no-print">
        <button onclick="window.print()" class="btn btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 6 2 18 2 18 9"/>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
            Cetak Struk
        </button>
        <a href="{{ route('transaksi.parkir.index') }}" class="btn btn-secondary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Kembali ke Transaksi
        </a>
    </div>

    <p class="copy no-print">© {{ date('Y') }} Neston Core &nbsp;·&nbsp; Smart Parking System</p>

</body>
</html>
