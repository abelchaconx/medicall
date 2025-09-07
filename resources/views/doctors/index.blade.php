@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4">
        <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-4" aria-label="Tabs" id="doctors-tabs">
                    <button data-tab="doctors" class="tab-btn py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white focus:outline-none" aria-current="page">DOCTORES</button>
                    <button data-tab="specialties" class="tab-btn py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white focus:outline-none">ESPECIALIDADES</button>
                </nav>
            </div>

            <div class="mt-4">
                <div id="tab-doctors" class="tab-panel">
                    @livewire('doctors')
                </div>
                <div id="tab-specialties" class="tab-panel hidden">
                    @livewire('specialties')
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function(){
        const tabs = document.querySelectorAll('#doctors-tabs .tab-btn');
        const panels = {
            doctors: document.getElementById('tab-doctors'),
            specialties: document.getElementById('tab-specialties')
        };

        function activate(name){
            tabs.forEach(t => {
                if (t.getAttribute('data-tab') === name) {
                    t.classList.add('border-blue-600','text-gray-900','dark:text-white');
                    t.classList.remove('border-transparent');
                } else {
                    t.classList.remove('border-blue-600','text-gray-900','dark:text-white');
                    t.classList.add('border-transparent');
                }
            });
            Object.keys(panels).forEach(k => {
                if (k === name) panels[k].classList.remove('hidden'); else panels[k].classList.add('hidden');
            });
        }

        tabs.forEach(t => t.addEventListener('click', function(){ activate(this.getAttribute('data-tab')) }));
        // default
        activate('doctors');
    })();
</script>
@endpush
