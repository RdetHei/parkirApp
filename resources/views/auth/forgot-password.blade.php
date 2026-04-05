<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lupa Password — NESTON</title>
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="min-h-screen bg-[#020617] text-slate-100 flex items-center justify-center p-6">
    <div class="w-full max-w-md glass-card rounded-3xl p-10 shadow-2xl">
        <a href="{{ route('login') }}" class="text-emerald-500 text-xs font-bold uppercase tracking-widest hover:text-emerald-400 mb-8 inline-flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke login
        </a>
        <h1 class="text-xl font-bold text-white mt-4 mb-2">Reset password</h1>
        <p class="text-slate-400 text-sm mb-8">Masukkan email terdaftar. Kami kirim tautan reset (berlaku sesuai konfigurasi Laravel).</p>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 text-emerald-400 text-sm border border-emerald-500/20">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
            @csrf
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Email</label>
                <input name="email" type="email" value="{{ old('email') }}" required autofocus
                    class="mt-2 w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none">
                @error('email')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="w-full py-4 bg-emerald-500 text-slate-950 text-xs font-bold uppercase tracking-widest rounded-2xl hover:bg-emerald-400">
                Kirim tautan reset
            </button>
        </form>
    </div>
</body>
</html>
