@extends('layouts.app')

@section('title','Area Parkir')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold">Area Parkir</h2>
        <a href="{{ route('area-parkir.create') }}" class="bg-green-600 text-white px-4 py-2 rounded">Create</a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3">ID</th>
                    <th class="px-6 py-3">Nama</th>
                    <th class="px-6 py-3">Kapasitas</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($areas as $area)
                <tr>
                    <td class="px-6 py-3">{{ $area->id_area }}</td>
                    <td class="px-6 py-3">{{ $area->nama_area }}</td>
                    <td class="px-6 py-3">{{ $area->kapasitas }}</td>
                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('area-parkir.edit', $area) }}" class="text-indigo-600">Edit</a>
                        <form action="{{ route('area-parkir.destroy', $area) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete?')">
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

    <div class="mt-4">{{ $areas->links() }}</div>
</div>
@endsection
