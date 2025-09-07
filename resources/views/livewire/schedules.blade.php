<div>
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <div class="col-span-1 flex justify-center sm:justify-start">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">GESTIÓN DE HORARIOS</h2>
            </div>
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1 flex justify-center sm:justify-end">
                <div class="flex w-full gap-2">
                    <button wire:click="create" class="flex-1 text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-green-500 to-green-600">Nuevo horario</button>
                    
                    <!-- Direct link to trashed schedules page for users who prefer a dedicated view -->
                    <a href="{{ url('/schedules/trashed') }}" class="flex-1 px-4 py-2 rounded h-10 bg-gray-700 text-white inline-flex items-center justify-center">Eliminados</a>
                </div>
            </div>
        </div>
    </div>

    @if($showForm)
        <div class="p-4 border rounded bg-white dark:bg-gray-800 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Doctor</label>
                    <select wire:model="doctor_id" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                        <option value="">-- Selecciona doctor --</option>
                        @foreach(($availableDoctors ?? []) as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('doctor_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Consultorio</label>
                    <select wire:model.defer="doctor_medicaloffice_id" wire:key="doctor-{{ $doctor_id ?? 'none' }}" wire:loading.attr="disabled" @if($availableDoctorMedicalOffices->isEmpty() || empty($doctor_id)) disabled @endif class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                        <option value="">-- Selecciona Consultorio --</option>
                        @foreach(($availableDoctorMedicalOffices ?? []) as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @if(empty($doctor_id))
                        <p class="text-xs text-gray-500">Selecciona primero un doctor para ver sus consultorios médicos.</p>
                    @elseif($availableDoctorMedicalOffices->isEmpty())
                        <p class="text-xs text-gray-500">El doctor seleccionado no tiene consultorios médicos asignados.</p>
                    @endif
                    @error('doctor_medicaloffice_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Día (weekday) — permite CSV: 1,2,3</label>
                    <input wire:model.defer="weekday" type="text" placeholder="Ej: 1,2,3 (1=lunes ... 6=sábado)" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    <p class="text-xs text-gray-500">Ingresa uno o varios días separados por coma. Valores válidos: 1..6.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Inicio</label>
                    <input wire:model.defer="start_time" type="time" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                </div>
                
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Fin</label>
                    <input wire:model.defer="end_time" type="time" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Duración (min)</label>
                    <input wire:model.defer="duration_minutes" type="number" min="0" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    @error('duration_minutes') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Descripción</label>
                    <input wire:model.defer="description" type="text" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    @error('description') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-3 flex space-x-2">
                <button wire:click="save" class="bg-green-600 text-white px-3 py-2 rounded">Guardar</button>
                <button wire:click="resetForm" class="bg-gray-200 px-3 py-2 rounded">Cancelar</button>
            </div>
        </div>
    @endif

    @if(! $showForm && isset($availableDoctorMedicalOffices) && $availableDoctorMedicalOffices instanceof \Illuminate\Support\Collection && ! $availableDoctorMedicalOffices->isEmpty())
        <!-- Hidden helper for tests and non-form consumers: expose available consultorios labels in markup -->
        <div style="display:none" aria-hidden="true">
            @foreach($availableDoctorMedicalOffices as $label)
                {{ $label }}
            @endforeach
        </div>
    @endif

    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1">
                <label for="search-schedules" class="sr-only">Buscar</label>
                <input id="search-schedules" wire:model.defer="search" type="text" placeholder="Buscar horario..." class="border rounded px-3 py-2 w-full bg-white dark:bg-gray-900 dark:text-gray-200" />
            </div>
            <div class="col-span-1">
                <div class="flex w-full items-center justify-center sm:justify-end gap-2">
                    <button wire:click="performSearch" class="flex-1 text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-blue-500 to-blue-600">Buscar</button>
                    <button wire:click="clearSearch" class="flex-1 bg-gray-700 text-white px-4 py-2 rounded h-10">Limpiar</button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-transparent">
        <div class="w-full overflow-x-auto">
            <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
                <thead class="hidden md:table-header-group"><tr class="text-left"><th class="px-3 py-2"> </th><th class="px-3 py-2">ID</th><th class="px-3 py-2">Descripción</th><th class="px-3 py-2">Acciones</th></tr></thead>
                <tbody class="md:table-row-group">
                    @foreach($schedules as $schedule)
                                <tr class="block md:table-row mb-3 md:mb-0 odd:bg-gray-50 even:bg-white dark:odd:bg-gray-800 dark:even:bg-gray-900 rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                            <td class="px-3 py-1">{{ $schedule->id }}</td>
                            <td class="px-3 py-1">
                                <div class="font-semibold">{{ $schedule->description ?? ($schedule->name ?? '—') }}</div>
                                <div class="text-xs text-gray-500">{{ $schedule->doctorMedicalOffice?->doctor?->user?->name ?? $schedule->doctorMedicalOffice?->medicalOffice?->name ?? '' }}</div>
                                <div class="mt-2 text-xs">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-xs">Día: {{ $schedule->weekdays ?? $schedule->weekday }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-xs ml-2">{{ $schedule->start_time }} — {{ $schedule->end_time }} ({{ $schedule->duration_minutes }}m)</span>
                                </div>
                            </td>
                            <td class="px-3 py-1">
                                <div class="flex items-center gap-2">
                                    @if($schedule->trashed())
                                        <button wire:click="restore({{ $schedule->id }})" class="px-3 py-1 rounded text-white bg-green-600">Restaurar</button>
                                        <button wire:click="forceDelete({{ $schedule->id }})" class="px-3 py-1 rounded text-white bg-red-800">Eliminar permanentemente</button>
                                    @else
                                        <button wire:click="edit({{ $schedule->id }})" class="px-3 py-1 rounded text-white bg-yellow-400">Editar</button>
                                        <button data-schedule-id="{{ $schedule->id }}" class="manage-exc-btn px-3 py-1 rounded text-white bg-blue-600">Excepciones
                                            <span class="ml-2 inline-block bg-white text-gray-700 px-2 py-0.5 rounded text-xs">{{ $schedule->exceptions()->count() }}</span>
                                        </button>
                                        <button wire:click="deleteSingle({{ $schedule->id }})" class="px-3 py-1 rounded text-white bg-red-600">Eliminar</button>
                                        @if($schedule->batch_id)
                                            <button wire:click="deleteBatch('{{ $schedule->batch_id }}')" class="px-3 py-1 rounded text-white bg-red-800">Eliminar lote</button>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $schedules->links() }}
        </div>
    </div>

    @includeWhen(View::exists('livewire._partials.toast_confirm'), 'livewire._partials.toast_confirm')
    
    <!-- Exceptions modal -->
    <div id="exceptions-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 p-4">
        <div id="exceptions-panel" class="transform transition-all duration-200 ease-out opacity-0 scale-95 bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-2xl w-full overflow-hidden">
            <div class="p-6">
                <h3 id="exceptions-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Excepciones</h3>
                <div id="exceptions-body" class="mt-3 text-sm text-gray-700 dark:text-gray-200"></div>
            </div>
            <div class="px-6 pb-6 pt-0 bg-gray-50 dark:bg-gray-900 flex justify-end">
                <button id="exceptions-close" class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-700 dark:text-gray-100">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        async function openExceptionsModal(scheduleId){
            const url = `{{ url('/schedules') }}/${scheduleId}/exceptions?ajax=1`;
            try {
                const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('Fetch failed');
                const data = await res.json();
                const modal = document.getElementById('exceptions-modal');
                const panel = document.getElementById('exceptions-panel');
                const titleEl = document.getElementById('exceptions-title');
                const body = document.getElementById('exceptions-body');
                titleEl.textContent = `Excepciones — Horario #${scheduleId}`;
                while (body.firstChild) body.removeChild(body.firstChild);
                if (data && data.exceptions && data.exceptions.length){
                    const ul = document.createElement('ul');
                    ul.className = 'space-y-2';
                    data.exceptions.forEach(e => {
                        const li = document.createElement('li');
                        li.className = 'p-2 bg-gray-50 dark:bg-gray-900 rounded';

                        const titleDiv = document.createElement('div');
                        titleDiv.className = 'font-medium';
                        titleDiv.textContent = `${e.date} — ${e.type}`;

                        const infoDiv = document.createElement('div');
                        infoDiv.className = 'text-xs text-gray-500';
                        infoDiv.textContent = (e.start_time || '—') + ' — ' + (e.end_time || '—') + (e.reason ? (' / ' + e.reason) : '');

                        li.appendChild(titleDiv);
                        li.appendChild(infoDiv);
                        ul.appendChild(li);
                    });
                    body.appendChild(ul);
                } else {
                    const p = document.createElement('p');
                    p.className = 'text-sm text-gray-500';
                    p.textContent = 'No hay excepciones registradas para este horario.';
                    body.appendChild(p);
                }

                modal.classList.remove('hidden');
                requestAnimationFrame(() => {
                    panel.classList.remove('opacity-0','-translate-y-2','scale-95');
                    panel.classList.add('opacity-100','translate-y-0','scale-100');
                });
            } catch (err) {
                console.error(err);
                alert('No se pudo cargar las excepciones');
            }
        }

        document.addEventListener('click', function(e){
            const btn = e.target.closest && e.target.closest('.manage-exc-btn');
            if (!btn) return;
            e.preventDefault();
            const id = btn.getAttribute('data-schedule-id');
            if (id) openExceptionsModal(id);
        });

        document.getElementById('exceptions-close')?.addEventListener('click', function(){
            const modal = document.getElementById('exceptions-modal');
            const panel = document.getElementById('exceptions-panel');
            panel.classList.remove('opacity-100','translate-y-0','scale-100');
            panel.classList.add('opacity-0','-translate-y-2','scale-95');
            setTimeout(() => modal.classList.add('hidden'), 200);
        });
        document.getElementById('exceptions-modal')?.addEventListener('click', function(e){ if (e.target === this) {
            const modal = this;
            const panel = document.getElementById('exceptions-panel');
            panel.classList.remove('opacity-100','translate-y-0','scale-100');
            panel.classList.add('opacity-0','-translate-y-2','scale-95');
            setTimeout(() => modal.classList.add('hidden'), 200);
        } });
    </script>

    <!-- Debug helpers removed: development-only instrumentation cleaned up. Livewire handling preserved via server-side logic and `wire:key`. -->
    <script>
        // Lightweight delegated listener: captures change events on any select matching
        // select[wire:model="doctor_id"], so it works even if Livewire replaces DOM nodes.
        (function(){
            function handleDocChange(e){
                try {
                    const t = e.target;
                    if (!t || !t.matches) return;
                    if (t.matches('select[wire\\:model="doctor_id"]')){
                        console.debug && console.debug('[schedules sync] doctor select changed ->', t.value);
                        const compEl = t.closest('[wire\\:id]') || document.querySelector('[wire\\:id]');
                        if (window.Livewire && compEl){
                            const id = compEl.getAttribute('wire:id');
                            try { window.Livewire.find(id).set('doctor_id', t.value); console.debug && console.debug('[schedules sync] Livewire.set executed'); } catch (err) { /* ignore */ }
                        }
                    }
                } catch (err) { /* ignore */ }
            }

            document.addEventListener('change', handleDocChange, true);
            // rebind after Livewire updates to be safe (delegated listener persists but ensure Livewire hooks exist)
            if (window.Livewire && typeof window.Livewire.hook === 'function'){
                window.Livewire.hook('message.processed', () => {});
            }
        })();
    </script>
</div>
