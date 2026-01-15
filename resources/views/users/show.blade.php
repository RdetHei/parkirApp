@extends('layouts.app')

@section('title', 'User')

@section('content')
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-bold mb-4">{{ $user->name }}</h2>
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Role:</strong> {{ $user->role }}</p>
    <div class="mt-4">
        <a href="{{ route('users.edit', $user) }}" class="bg-indigo-600 text-white px-4 py-2 rounded">Edit</a>
        <a href="{{ route('users.index') }}" class="ml-2">Back</a>
    </div>
</div>
@endsection
