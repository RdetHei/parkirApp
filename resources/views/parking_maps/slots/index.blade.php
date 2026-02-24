@extends('layouts.app')

@section('title', 'Slot Peta: ' . $parkingMap->name)

@section('content')
<div class="p-4 sm:p-6 lg:p-8">
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center justify-between">
            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            <button type="button" onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">×</button>
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900">Slot: {{ $parkingMap->name }} ({{ $parkingMap->code }})</h2>
                <p class="text-xs text-gray-500">Kelola slot parkir untuk peta ini. Link ke Area Parkir opsional.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('parking-maps.edit', $parkingMap) }}" class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800">Edit layout</a>
                <a href="{{ route('parking-maps.slots.create', $parkingMap) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">
                    + Tambah slot
                </a>
            </div>
        </div>

        @if($slots->count())
        <table class="w-full table-auto divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Posisi (x, y)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ukuran</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Area</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kamera</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Catatan</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($slots as $slot)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-semibold">{{ $slot->code }}</td>
                    <td class="px-6 py-4 text-sm">{{ $slot->x }}, {{ $slot->y }}</td>
                    <td class="px-6 py-4 text-sm">{{ $slot->width }}×{{ $slot->height }}</td>
                    <td class="px-6 py-4 text-sm">{{ $slot->areaParkir->nama_area ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $slot->camera->nama ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($slot->notes, 30) ?: '—' }}</td>
                    <td class="px-6 py-4 text-sm text-right">
                        <a href="{{ route('parking-maps.slots.edit', [$parkingMap, $slot]) }}" class="text-amber-600 hover:text-amber-800 mr-2">Edit</a>
                        <form action="{{ route('parking-maps.slots.destroy', [$parkingMap, $slot]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus slot ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t bg-gray-50">{{ $slots->links() }}</div>
        @else
        <div class="px-6 py-8 text-center text-gray-500">
            <p class="mb-4">Belum ada slot untuk peta ini.</p>
            <a href="{{ route('parking-maps.slots.create', $parkingMap) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Tambah slot</a>
        </div>
        @endif
    </div>
</div>
@endsection
