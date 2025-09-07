@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <div class="p-4 bg-white rounded shadow">
            <p>Esta vista ha sido reemplazada. Ver los consultorios eliminados <a href="{{ route('medical-offices.trashed') }}" class="text-blue-600">aquí</a>.</p>
        </div>
    </div>
@endsection
