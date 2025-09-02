@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold dark:text-gray-100">USUARIOS ELIMINADOS</h1>
        <a href="{{ route('users.index') }}" class="text-sm text-blue-600 w-full md:w-auto text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 transition disabled:opacity-50 disabled:cursor-not-allowed">Volver a usuarios</a>
    </div>

    @if(session()->has('toast'))
        <div class="mb-4 text-sm text-green-700">{{ session('toast.message') }}</div>
    @endif

    <div>
        <livewire:trashed-users />
    </div>
</div>
@endsection
