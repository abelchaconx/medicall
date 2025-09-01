@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-xl mb-4">UI Components Demo</h1>

    {{-- SweetAlert include --}}
    @include('components.ui.sweetalert')

    <div class="mb-6">
        <button onclick="window.SwalHelper.confirm({title:'Delete demo', text:'This is a demo'}) .then(r=>{ if(r.isConfirmed) window.SwalHelper.toast({icon:'success', title:'Deleted'}) })"
            class="px-4 py-2 bg-red-600 text-white rounded">Demo SweetAlert</button>
    </div>

    <div class="mb-6">
        {{-- Livewire toast example (emit event to show toast) --}}
        <button wire:click="$emit('showToast', 'success', 'Operación exitosa')" class="px-4 py-2 bg-green-600 text-white rounded">Show Toast</button>
    </div>

    <div class="mb-6">
        <h2 class="font-semibold">Doctors table</h2>
        @livewire('smart-table', ['modelClass' => \App\Models\Doctor::class])
    </div>
</div>

@endsection
