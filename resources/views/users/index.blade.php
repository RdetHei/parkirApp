@extends('layouts.app')

@section('title', 'Team Management')

@section('content')
<div class="p-8 relative z-10">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
        <div>
            <div class="flex items-center gap-3 mb-3">
                <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 text-[10px] font-bold uppercase tracking-widest rounded-full border border-emerald-500/20">
                    Access Control
                </span>
            </div>
            <h1 class="text-4xl font-bold tracking-tight text-white">System <span class="text-emerald-500">Users</span></h1>
            <p class="text-slate-400 text-sm mt-2">Manage personnel access and system roles.</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('users.create') }}" class="group relative px-6 py-3 bg-emerald-500 text-slate-950 font-bold text-xs uppercase tracking-widest rounded-xl transition-all hover:bg-emerald-400 hover:shadow-[0_0_20px_rgba(16,185,129,0.4)] flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Invite User
            </a>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="mb-8 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4 flex items-center gap-4 animate-fade-in">
            <div class="w-8 h-8 bg-emerald-500/20 rounded-lg flex items-center justify-center text-emerald-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <p class="text-sm font-bold text-emerald-500 uppercase tracking-widest">{{ session('success') }}</p>
        </div>
    @endif

    <!-- Main Data Table -->
    <div class="card-pro !p-0 overflow-hidden shadow-2xl">
        <div class="px-8 py-6 border-b border-white/5 bg-white/[0.02] flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-sm font-bold text-white uppercase tracking-widest">Team Directory <span class="text-slate-500 ml-2 font-medium">({{ $users->total() }} total)</span></h2>
            
            <form action="{{ route('users.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="relative min-w-[240px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, email, phone..." 
                           class="block w-full pl-10 pr-3 py-2 bg-slate-900/50 border border-white/10 rounded-xl text-xs text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all">
                </div>
                
                <select name="role" onchange="this.form.submit()" 
                        class="bg-slate-900/50 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all min-w-[120px]">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="petugas" {{ request('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
                    <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                </select>

                @if(request()->anyFilled(['q', 'role']))
                    <a href="{{ route('users.index') }}" class="p-2 text-slate-500 hover:text-white transition-colors" title="Clear Filters">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/[0.01] text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        <th class="px-8 py-4">ID</th>
                        <th class="px-8 py-4">Identity</th>
                        <th class="px-8 py-4">Authentication</th>
                        <th class="px-8 py-4">Role & Permissions</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($users as $user)
                        <tr class="hover:bg-white/[0.02] transition-colors group">
                            <td class="px-8 py-5">
                                <span class="text-[10px] font-mono font-bold text-emerald-500/80">#{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <x-user-avatar :user="$user" size="md" class="!text-emerald-500 !border-white/5 group-hover:!border-emerald-500/30 transition-colors" />
                                    <div>
                                        <p class="text-sm font-bold text-white tracking-tight">{{ $user->name }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            @if($user->status_aktif ?? true)
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                                                <span class="text-[9px] font-black text-emerald-500 uppercase">Active</span>
                                            @else
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-medium text-slate-300">{{ $user->email }}</span>
                                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mt-1">Primary Email</span>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                @php
                                    $roleStyles = [
                                        'admin' => 'bg-indigo-500/10 text-indigo-500 border-indigo-500/20',
                                        'petugas' => 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20',
                                        'owner' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                    ];
                                    $roleStyle = $roleStyles[$user->role] ?? 'bg-slate-800 text-slate-400 border-white/5';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[9px] font-black uppercase border {{ $roleStyle }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right space-x-2">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('users.edit', $user) }}"
                                       class="p-2 bg-amber-500/10 hover:bg-amber-500 text-amber-500 hover:text-slate-950 rounded-lg border border-amber-500/20 transition-all"
                                       title="Modify Account">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Archive this user? Access will be revoked immediately.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-2 bg-rose-500/10 hover:bg-rose-500 text-rose-500 hover:text-white rounded-lg border border-rose-500/20 transition-all"
                                                title="Archive User">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-slate-900 border border-white/5 rounded-[2rem] flex items-center justify-center text-slate-700 mb-6">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-white mb-2">No users registered</h3>
                                    <p class="text-slate-500 text-sm max-w-xs mx-auto">Invite your team members to start collaborating on the parking system.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-8 py-6 border-t border-white/5 bg-white/[0.01]">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
