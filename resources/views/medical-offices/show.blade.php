@php
$office = \App\Models\MedicalOffice::with('doctors.user')->find($officeId);
@endphp

@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <div class="bg-white dark:bg-gray-800 shadow rounded p-4">
        <h2 class="text-xl font-semibold mb-2">{{ $office->name ?? 'Consultorio' }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $office->address_line ?? 'Sin dirección' }}</p>
        <p class="text-sm text-gray-500 mt-1">{{ $office->province ?? '' }} · {{ $office->city ?? '' }}</p>

        <div class="mt-4">
            <h3 class="font-medium">Doctores asignados</h3>
            <ul class="mt-2 space-y-2">
                @forelse($office->doctors ?? [] as $doc)
                    <li class="p-2 bg-gray-50 dark:bg-gray-900 rounded">
                        <div class="text-sm font-medium">{{ $doc->user?->name ?? '—' }}</div>
                                <div class="text-xs mt-1">
                                    @if($doc->specialties && $doc->specialties->isNotEmpty())
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($doc->specialties as $s)
                                                <span class="text-xs px-2 py-0.5 rounded text-black dark:text-white" style="background: {{ $s->color_translucent }}; border: 1px solid {{ $s->color }};">{{ $s->name }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        —
                                    @endif
                                </div>
                    </li>
                @empty
                    <li class="text-sm text-gray-500">No hay doctores asignados.</li>
                @endforelse
            </ul>
        </div>

        <div class="mt-4">
            <a href="{{ route('medical-offices.index') }}" class="text-blue-600">Volver a consultorios</a>
        </div>
    </div>
</div>
@endsection
