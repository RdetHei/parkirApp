<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Password Baru — NESTON</title>
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="min-h-screen bg-[#020617] text-slate-100 flex items-center justify-center p-6">
    <div class="w-full max-w-md glass-card rounded-3xl p-10 shadow-2xl">
        <h1 class="text-xl font-bold text-white mb-2">Password baru</h1>
        <p class="text-slate-400 text-sm mb-8">Token dikirim lewat email; masa berlaku mengikuti <code class="text-emerald-400">config/auth.php</code> (default 60 menit).</p>

        <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Email</label>
                <input name="email" type="email" value="{{ old('email', $request->email) }}" required
                    class="mt-2 w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none">
                @error('email')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Password baru</label>
                <input name="password" type="password" required
                    class="mt-2 w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none">
                @error('password')
                    <p class="mt-2 text-xs text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1">Konfirmasi password</label>
                <input name="password_confirmation" type="password" required
                    class="mt-2 w-full px-4 py-4 bg-slate-950/50 border border-white/5 rounded-2xl text-white text-sm focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none">
            </div>
            <button type="submit" class="w-full py-4 bg-emerald-500 text-slate-950 text-xs font-bold uppercase tracking-widest rounded-2xl hover:bg-emerald-400">
                Simpan password
            </button>
        </form>
    </div>
</body>
</html>
