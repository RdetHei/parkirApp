@extends('layouts.app')

@section('title','Transaksi')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Transaksi</h2>
        <a href="{{ route('transaksi.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">Create</a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Kendaraan</th>
                    <th class="px-6 py-3">Waktu Masuk</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td class="px-6 py-3">{{ $item->id_parkit }}</td>
                    <td class="px-6 py-3">{{ $item->id_kendaraan }}</td>
                    <td class="px-6 py-3">{{ $item->waktu_masuk }}</td>
                    <td class="px-6 py-3">{{ $item->status }}</td>
                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('transaksi.edit', $item) }}" class="text-indigo-600">Edit</a>
                        <form action="{{ route('transaksi.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete?')">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $items->links() }}</div>
</div>
@endsection
