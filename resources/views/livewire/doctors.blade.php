<div id="livewire-doctors">
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <div class="col-span-1 flex justify-center sm:justify-start">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">GESTIÓN DE DOCTORES</h2>
            </div>
            <div class="col-span-1 hidden sm:block"></div>
                    <div class="col-span-1 flex justify-center sm:justify-end">
                <div class="flex w-full gap-2">
                    <button wire:click="create" class="flex-1 text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-green-500 to-green-600">Nuevo doctor</button>
                    <a href="{{ route('doctors.trashed') }}" class="flex-1 inline-flex items-center justify-center bg-gray-700 text-white px-4 py-2 rounded h-10">
                        Eliminados
                        @if(isset($trashedCount) && $trashedCount > 0)
                            <span class="ml-2 inline-flex items-center justify-center bg-red-600 text-white text-xs px-2 py-0.5 rounded">{{ $trashedCount ?? 0 }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1">
                <label for="search-doctors" class="sr-only">Buscar</label>
                <input id="search-doctors" wire:model.defer="search" type="text" placeholder="Buscar doctor por licencia o bio..." class="border rounded px-3 py-2 w-full bg-white dark:bg-gray-900 dark:text-gray-200" />
            </div>
            <div class="col-span-1">
                <div class="flex w-full items-center justify-center sm:justify-end gap-2">
                    <button wire:click="performSearch" class="flex-1 text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-blue-500 to-blue-600">Buscar</button>
                    <button wire:click="clearSearch" class="flex-1 bg-gray-700 text-white px-4 py-2 rounded h-10">Limpiar</button>
                </div>
            </div>
        </div>
    </div>

    @if($showForm)
        <div class="p-4 border rounded bg-white dark:bg-gray-800 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Usuario</label>
                    <select wire:model.defer="user_id" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                        <option value="">Seleccionar usuario</option>
                        @foreach($availableUsers ?? [] as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Número de licencia</label>
                    <input wire:model.defer="license_number" type="text" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Especialidad</label>
                    <select wire:model.defer="specialty_id" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                        <option value="">-- Seleccionar --</option>
                        @foreach($availableSpecialties as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Biografía</label>
                    <textarea wire:model.defer="bio" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200"></textarea>
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Consultorios (puede seleccionar varios)</label>
                    <select wire:model.defer="medical_office_ids" multiple size="5" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                        @foreach($availableMedicalOffices as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-3 flex space-x-2">
                <button wire:click="save" class="bg-green-600 text-white px-3 py-2 rounded">Guardar</button>
                <button wire:click="resetForm" class="bg-gray-200 px-3 py-2 rounded">Cancelar</button>
                <span wire:loading wire:target="save" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Guardando...</span>
            </div>
        </div>
    @endif

    <div class="bg-transparent">
        <div class="w-full overflow-x-auto">
            <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
                <thead class="hidden md:table-header-group bg-gray-50 dark:bg-gray-900">
                    <tr class="text-left">
                        <th class="px-3 py-2">ID</th>
                        <th class="px-3 py-2">Usuario</th>
                        <th class="px-3 py-2">Especialidad</th>
                        <th class="px-3 py-2">Consultorio</th>
                        <th class="px-3 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody class="md:table-row-group">
                    @foreach($doctors as $doctor)
                        <tr class="block md:table-row mb-3 md:mb-0 odd:bg-gray-50 even:bg-white dark:odd:bg-gray-800 dark:even:bg-gray-900 rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                            <td class="px-3 py-1"><div class="hidden md:block">{{ $doctor->id }}</div></td>
                            <td class="px-3 py-1">{{ $doctor->user?->name ?? '—' }}</td>
                            <td class="px-3 py-1">
                                <div class="text-sm">
                                    @if($doctor->specialties && $doctor->specialties->isNotEmpty())
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($doctor->specialties as $spec)
                                                <span class="text-xs px-2 py-0.5 rounded text-black dark:text-white" style="background: {{ $spec->color_translucent }}; border: 1px solid {{ $spec->color }};">{{ $spec->name }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        —
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-1">
                                <div class="text-sm">
                                    @if($doctor->medicalOffices && $doctor->medicalOffices->isNotEmpty())
                                        <ul class="space-y-1">
                                            @foreach($doctor->medicalOffices as $office)
                                                <li class="text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100">
                                                    <a href="{{ route('medical-offices.show', $office->id) }}?ajax=1" class="hover:underline consultorio-link" data-id="{{ $office->id }}">{{ $office->name }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-1">
                                <div class="flex items-center gap-2">
                                    <button wire:click="edit({{ $doctor->id }})" class="px-3 py-1 rounded text-white bg-yellow-400">Editar</button>
                                    
                                    @if($doctor->trashed())
                                        <button onclick="confirmAction('restore', {{ $doctor->id }})" class="px-3 py-1 rounded text-white bg-green-600">Restaurar</button>
                                    @else
                                        <button onclick="confirmAction('delete', {{ $doctor->id }})" class="px-3 py-1 rounded text-white bg-red-600">Eliminar</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-3">{{ $doctors->links() }}</div>
    </div>

    @includeWhen(View::exists('livewire._partials.toast_confirm'), 'livewire._partials.toast_confirm')

    
        <!-- Consultorio modal (AJAX) -->
        <div id="consultorio-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div id="consultorio-panel" class="transform transition-all duration-200 ease-out opacity-0 scale-95 bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-2xl w-full overflow-hidden">
                <div class="p-6">
                    <h3 id="consultorio-title" class="text-lg font-semibold text-gray-900 dark:text-gray-100">Consultorio</h3>
                    <div id="consultorio-body" class="mt-3 text-sm text-gray-700 dark:text-gray-200"></div>
                </div>
                <div class="px-6 pb-6 pt-0 bg-gray-50 dark:bg-gray-900 flex justify-end">
                    <button id="consultorio-close" class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-700 dark:text-gray-100">Cerrar</button>
                </div>
            </div>
        </div>

        <script>
            async function openConsultorioModal(officeId){
                const url = `{{ url('/medical-offices') }}/${officeId}?ajax=1`;
                try {
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error('Fetch failed');
                    const data = await res.json();
                            const modal = document.getElementById('consultorio-modal');
                            const titleEl = document.getElementById('consultorio-title');
                            const bodyEl = document.getElementById('consultorio-body');
                            titleEl.textContent = data.name || 'Consultorio';
                            // Clear body
                            while (bodyEl.firstChild) bodyEl.removeChild(bodyEl.firstChild);

                            const pAddr = document.createElement('p');
                            pAddr.className = 'text-sm text-gray-600';
                            pAddr.textContent = data.address || 'Sin dirección';
                            bodyEl.appendChild(pAddr);

                            const pLoc = document.createElement('p');
                            pLoc.className = 'text-xs text-gray-500 mt-1';
                            pLoc.textContent = [data.province || '', data.city || ''].filter(Boolean).join(' · ');
                            bodyEl.appendChild(pLoc);

                            if (data.doctors && data.doctors.length) {
                                const h4 = document.createElement('h4');
                                h4.className = 'mt-3 font-medium';
                                h4.textContent = 'Doctores asignados';
                                bodyEl.appendChild(h4);

                                const ul = document.createElement('ul');
                                ul.className = 'mt-2 space-y-2';
                                data.doctors.forEach(d => {
                                    const li = document.createElement('li');
                                    li.className = 'p-2 bg-gray-50 dark:bg-gray-900 rounded';

                                    const nameDiv = document.createElement('div');
                                    nameDiv.className = 'text-sm font-medium';
                                    nameDiv.textContent = d.name || '—';
                                    li.appendChild(nameDiv);

                                    const specDiv = document.createElement('div');
                                    specDiv.className = 'text-xs mt-1';
                                    if (d.specialties && d.specialties.length) {
                                        const wrap = document.createElement('div');
                                        wrap.className = 'flex flex-wrap gap-2';
                                        d.specialties.forEach(s => {
                                            const span = document.createElement('span');
                                            span.className = 'text-xs px-2 py-0.5 rounded';
                                            span.style.background = s.color_translucent || 'rgba(0,0,0,0.05)';
                                            span.style.border = '1px solid ' + (s.color || '#000');
                                            span.textContent = s.name;
                                            wrap.appendChild(span);
                                        });
                                        specDiv.appendChild(wrap);
                                    } else {
                                        specDiv.textContent = 'Especialidad(s): —';
                                    }
                                    li.appendChild(specDiv);

                                    ul.appendChild(li);
                                });
                                bodyEl.appendChild(ul);
                            } else {
                                const pNone = document.createElement('p');
                                pNone.className = 'text-sm text-gray-500 mt-2';
                                pNone.textContent = 'No hay doctores asignados.';
                                bodyEl.appendChild(pNone);
                            }

                            const panel = document.getElementById('consultorio-panel');
                            modal.classList.remove('hidden');
                            requestAnimationFrame(() => {
                                panel.classList.remove('opacity-0','-translate-y-2','scale-95');
                                panel.classList.add('opacity-100','translate-y-0','scale-100');
                            });
                } catch (err) {
                    console.error(err);
                    alert('No se pudo cargar el consultorio.');
                }
            }

            document.addEventListener('click', function(e){
                const a = e.target.closest && e.target.closest('.consultorio-link');
                if (!a) return;
                e.preventDefault();
                const id = a.getAttribute('data-id');
                if (id) openConsultorioModal(id);
            });

            document.getElementById('consultorio-close')?.addEventListener('click', function(){
                const modal = document.getElementById('consultorio-modal');
                const panel = document.getElementById('consultorio-panel');
                panel.classList.remove('opacity-100','translate-y-0','scale-100');
                panel.classList.add('opacity-0','-translate-y-2','scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            });
            document.getElementById('consultorio-modal')?.addEventListener('click', function(e){ if (e.target === this) {
                const modal = this;
                const panel = document.getElementById('consultorio-panel');
                panel.classList.remove('opacity-100','translate-y-0','scale-100');
                panel.classList.add('opacity-0','-translate-y-2','scale-95');
                setTimeout(() => modal.classList.add('hidden'), 200);
            } });
        </script>
</div>
