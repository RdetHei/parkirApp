@extends('layouts.app')

@section('title', $title ?? 'RFID Access Demo')

@section('content')
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-2xl mx-auto bg-white/5 border border-white/10 rounded-2xl p-6">
            <div class="text-xl font-extrabold text-white">{{ $title ?? 'RFID Access Demo' }}</div>
            <div class="text-sm text-slate-300 mt-2">
                Jika Anda bisa melihat halaman ini, berarti Anda sudah melakukan scan kartu pada halaman akses.
            </div>

            <div class="mt-4 text-xs text-slate-300">
                Session key: <code class="text-slate-200">rfid_access_user_id</code>
            </div>
        </div>
    </div>
@endsection

