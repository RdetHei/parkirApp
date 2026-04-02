<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email — NESTON</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="min-h-screen bg-[#020617] text-slate-100 flex items-center justify-center p-6">
    <div class="w-full max-w-md glass-card rounded-3xl p-10 shadow-2xl">
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-envelope-open-text text-slate-950 text-xl"></i>
            </div>
            <h1 class="text-xl font-bold text-white">Verifikasi email Anda</h1>
            <p class="text-slate-400 text-sm mt-2">Kami mengirim tautan ke <strong class="text-white">{{ auth()->user()->email }}</strong>. Klik tautan di email untuk mengaktifkan akun.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 text-emerald-400 text-sm font-medium border border-emerald-500/20">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="space-y-4">
            @csrf
            <button type="submit" class="w-full py-4 bg-emerald-500 text-slate-950 text-xs font-bold uppercase tracking-widest rounded-2xl hover:bg-emerald-400 transition-colors">
                Kirim ulang email verifikasi
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="mt-4">
            @csrf
            <button type="submit" class="w-full py-3 text-slate-500 text-xs font-bold uppercase tracking-widest hover:text-white transition-colors">
                Keluar
            </button>
        </form>
    </div>
</body>
</html>
