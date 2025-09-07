@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white dark:bg-gray-800 rounded p-4">
            <div class="flex items-center gap-2 mb-4">
                <button id="tab-schedules" class="px-4 py-2 bg-gray-700 text-white rounded">Horarios</button>
                <button id="tab-exceptions" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded">Excepciones</button>
            </div>

            <div id="panel-schedules">
                @livewire(\App\Http\Livewire\Schedules::class)
            </div>

            <div id="panel-exceptions" class="hidden">
                @livewire(\App\Http\Livewire\ScheduleExceptions::class)
            </div>
        </div>
    </div>

    <script>
        document.getElementById('tab-schedules').addEventListener('click', function(){
            document.getElementById('panel-schedules').classList.remove('hidden');
            document.getElementById('panel-exceptions').classList.add('hidden');
            this.classList.add('bg-gray-700','text-white');
            document.getElementById('tab-exceptions').classList.remove('bg-gray-700','text-white');
        });
        document.getElementById('tab-exceptions').addEventListener('click', function(){
            document.getElementById('panel-schedules').classList.add('hidden');
            document.getElementById('panel-exceptions').classList.remove('hidden');
            this.classList.add('bg-gray-700','text-white');
            document.getElementById('tab-schedules').classList.remove('bg-gray-700','text-white');
        });
    </script>
@endsection
