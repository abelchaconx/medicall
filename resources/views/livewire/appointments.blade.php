<div id="livewire-component-root">
    <div id="page-root">
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <div class="col-span-1 flex justify-center sm:justify-start">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">GESTIÓN DE CITAS</h2>
            </div>
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1 flex justify-center sm:justify-end">
                <div class="flex w-full gap-2">
                    <button wire:click="create" class="flex-1 text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-green-500 to-green-600 hover:opacity-95 shadow-sm">Nuevo cita</button>
                    <a href="{{ route('appointments.trashed') }}" class="flex-1 inline-flex items-center justify-center bg-gray-700 text-white px-4 py-2 rounded h-10 hover:bg-gray-800">Eliminados</a>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1">
                <input wire:model.defer="search" type="text" placeholder="Buscar..." class="border rounded px-3 py-2 w-full bg-white dark:bg-gray-900 dark:text-gray-200 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900" />
            </div>
            <div class="col-span-1 flex justify-center sm:justify-end">
                <div class="flex gap-2 w-full">
                    <button wire:click="performSearch" class="flex-1 text-white px-4 py-2 rounded h-10 bg-blue-600 hover:bg-blue-700">Buscar</button>
                    <button wire:click="clearSearch" class="flex-1 bg-gray-700 text-white px-4 py-2 rounded h-10 hover:bg-gray-800">Limpiar</button>
                </div>
            </div>
        </div>
    </div>

    {{-- calendar/select2 area will be rendered inside the form section for new appointments --}}

    @if($showForm)
        @if($appointmentId)
            {{-- Edit existing appointment: keep the current form layout --}}
            <div class="p-4 border rounded bg-white dark:bg-gray-800 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    {{-- ...existing code... --}}
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Paciente</label>
                        <select wire:model.defer="patient_id" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                            <option value="">-- Selecciona paciente --</option>
                            @foreach(($availablePatients ?? []) as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('patient_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Consultorio</label>
                        <select wire:model="doctor_medicaloffice_id" wire:change="selectDoctor($event.target.value)" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                            <option value="">-- Selecciona consultorio --</option>
                            @foreach(($availableDoctorMedicalOffices ?? []) as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('doctor_medicaloffice_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                        <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Inicio</label>
                        <input wire:model.defer="start_datetime" type="datetime-local" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                        @error('start_datetime') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Fin</label>
                        <input wire:model.defer="end_datetime" type="datetime-local" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    </div>
                    @if($appointmentId)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Estado</label>
                        <select wire:model.defer="status" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                            <option value="">-- Selecciona estado --</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="atendido">Atendido</option>
                        </select>
                        @error('status') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    @endif
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Notas</label>
                        <textarea wire:model.defer="notes" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200"></textarea>
                    </div>
                </div>
                <!-- Buttons moved to footer below the 3-column grid -->
            </div>
        @else
            {{-- New appointment booking UI: 3 columns secuenciales --}}

            {{-- ejemplos removed per request --}}
            <div class="p-4 border rounded bg-white dark:bg-gray-800 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Col 1: Select2 + calendario (siempre visible) --}}
                            <div>
                                <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-200">1. Buscar consultorio (doctor - consultorio)</label>
                        {{-- Custom dropdown with integrated search input --}}
                        <div class="relative" id="doctor-dropdown-root">
                            <!-- Hidden input bound to Livewire so JS can set value reliably regardless of client API -->
                            <input id="doctor-medicaloffice-hidden" type="hidden" wire:model="doctor_medicaloffice_id" />
                            <button type="button" id="doctor-dropdown-button" onclick="toggleDoctorDropdown(event)" class="w-full text-left mt-1 border rounded px-2 py-2 bg-white dark:bg-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800">
                                <span id="doctor-dropdown-selected">{{ $availableDoctorMedicalOffices[$doctor_medicaloffice_id] ?? '-- Selecciona consultorio --' }}</span>
                            </button>

                            <div id="doctor-dropdown" class="absolute z-40 left-0 right-0 mt-1 bg-white dark:bg-gray-800 border rounded shadow-lg hidden">
                                <div class="p-2 relative">
                                    <input id="doctor-dropdown-input" wire:model.debounce.150ms="consultorio_search" type="text" placeholder="Escribe 3+ letras para buscar" class="w-full border rounded px-2 py-1 pr-8 bg-white dark:bg-gray-900 dark:text-gray-200 text-sm placeholder-gray-500 dark:placeholder-gray-400" onkeydown="event.stopPropagation()" oninput="doctorDropdownInputChanged(this.value)" />
                                    <button type="button" id="doctor-dropdown-clear" onclick="clearDoctorDropdownFilter()" title="Limpiar" aria-label="Limpiar búsqueda" class="hidden absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:text-gray-300 dark:hover:text-white p-1 rounded focus:outline-none focus:ring-2 focus:ring-blue-300">
                                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="none" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l8 8M14 6l-8 8" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="max-h-48 overflow-auto divide-y text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-800">
                                    @if(! $consultorio_search || strlen(trim($consultorio_search)) < 3)
                                        <!-- <div class="p-3 text-sm text-gray-500">Escribe 3 o más letras para filtrar resultados.</div> -->
                                        @if(!empty($availableDoctorMedicalOffices))
                                            <!-- <div class="p-2 text-xs text-gray-400">O selecciona uno de los recientes:</div> -->
                                            @foreach(array_slice($availableDoctorMedicalOffices->toArray(), 0, 8, true) as $id => $label)
                                                <div class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer text-gray-800 dark:text-gray-100" onclick="selectDoctorPlace({{ $id }})">{{ $label }}</div>
                                            @endforeach
                                        @endif
                                    @else
                                        @if(count($availableDoctorMedicalOffices) === 0)
                                            <div class="p-3 text-sm text-gray-500 dark:text-gray-400">No se encontraron coincidencias.</div>
                                        @else
                                            @foreach($availableDoctorMedicalOffices as $id => $label)
                                                <div class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer text-gray-800 dark:text-gray-100" onclick="selectDoctorPlace({{ $id }})">{{ $label }}</div>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        



                        {{-- Calendario solo visible si hay consultorio seleccionado --}}
                        @if($doctor_medicaloffice_id)
                            <div class="mt-3 p-2 bg-white dark:bg-gray-800 rounded border">
                                <h4 class="text-sm font-medium mb-2 text-gray-700 dark:text-gray-200">2. Seleccionar fecha</h4>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex gap-2">
                                        <button wire:click="prevMonth" class="px-2 py-1 border rounded bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200">&lt;</button>
                                        <button wire:click="nextMonth" class="px-2 py-1 border rounded bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200">&gt;</button>
                                    </div>
                                    <div class="font-semibold text-sm text-gray-700 dark:text-gray-200">{{ \Carbon\Carbon::parse($calendarMonth)->format('F Y') }}</div>
                                </div>
                                <div class="grid grid-cols-7 gap-1 text-xs">
                                    @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $d)
                                        <div class="text-center font-medium">{{ $d }}</div>
                                    @endforeach
                                    @php
                                        $start = \Carbon\Carbon::parse($calendarMonth)->startOfMonth();
                                        $end = \Carbon\Carbon::parse($calendarMonth)->endOfMonth();
                                        $pad = ($start->isoWeekday() === 7) ? 6 : ($start->isoWeekday()-1);
                                        $days = [];
                                        for ($i=0;$i<$pad;$i++) $days[] = null;
                                        for ($d = $start->copy(); $d->lte($end); $d->addDay()) $days[] = $d->format('Y-m-d');
                                    @endphp
                                    @foreach($days as $day)
                                        @if(!$day)
                                            <div class="h-12 border rounded bg-gray-50 dark:bg-gray-700"></div>
                                        @else
                                            @php
                                                    $isSelected = $selected_date === $day;
                                                    $dayClass = 'h-12 border rounded p-1 cursor-pointer text-xs ';
                                                    $info = $dailyAvailability[$day] ?? null;
                                                    if ($isSelected) {
                                                        $dayClass .= 'bg-blue-200 dark:bg-blue-800 border-blue-400';
                                                    } else {
                                                        // priority rules:
                                                        // 1) no schedules -> gray
                                                        // 2) available == 0 -> red
                                                        // 3) available/total < 0.5 -> yellow
                                                        // 4) booked > 0 -> green
                                                        // 5) otherwise green
                                                        if (! $info || ! ($info['hasSchedules'] ?? false)) {
                                                            $dayClass .= 'bg-gray-100 dark:bg-gray-800 border-gray-200 text-gray-700';
                                                        } else {
                                                            $available = $info['available'] ?? 0;
                                                            $total = $info['total'] ?? 0;
                                                            $booked = $info['booked'] ?? 0;

                                                            if ($total === 0) {
                                                                $dayClass .= 'bg-gray-100 dark:bg-gray-800 border-gray-200 text-gray-700';
                                                            } elseif ($available === 0) {
                                                                $dayClass .= 'bg-red-100 dark:bg-red-900 border-red-300 text-red-800';
                                                            } elseif ($total > 0 && ($available / $total) < 0.5) {
                                                                $dayClass .= 'bg-yellow-100 dark:bg-yellow-900 border-yellow-300 text-yellow-800';
                                                            } elseif ($booked > 0) {
                                                                $dayClass .= 'bg-green-100 dark:bg-green-900 border-green-300 text-green-800';
                                                            } else {
                                                                $dayClass .= 'bg-green-50 dark:bg-green-900 border-green-200';
                                                            }
                                                        }
                                                    }
                                                @endphp
                                            <div class="{{ $dayClass }}" wire:click="selectDate('{{ $day }}')">
                                                <div class="text-gray-800 dark:text-gray-100">{{ \Carbon\Carbon::parse($day)->format('j') }}</div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="mt-3 p-4 bg-gray-50 dark:bg-gray-700 rounded border text-center text-sm text-gray-500">
                                ⏳ Selecciona un consultorio para ver el calendario
                            </div>
                        @endif
                    </div>

                    {{-- Col 2: Horas disponibles (solo visible si hay fecha seleccionada) --}}
                    <div>
                        @if($selected_date && $doctor_medicaloffice_id)
                            <div class="p-3 bg-white dark:bg-gray-800 rounded border">
                                <h3 class="font-semibold mb-2">3. Disponibles para {{ \Carbon\Carbon::parse($selected_date)->format('d/m/Y') }}</h3>
                                @if(empty($available_hours))
                                    <div class="text-sm text-gray-500 dark:text-gray-400">No hay horas disponibles para esta fecha.</div>
                                @else
                                    <div class="grid grid-cols-2 gap-2">
                                        @foreach($available_hours as $slot)
                                            <button wire:click="selectTimeSlot('{{ $slot['start'] }}', {{ $slot['schedule_id'] ?? 'null' }})"
                                                    class="px-2 py-1 bg-green-100 dark:bg-green-900 hover:bg-green-200 dark:hover:bg-green-800 rounded text-sm transition-colors text-gray-800 dark:text-gray-100">
                                                {{ $slot['start'] }} - {{ $slot['end'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @elseif($doctor_medicaloffice_id)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded border text-center text-sm text-gray-500">
                                📅 Selecciona una fecha para ver las horas disponibles
                            </div>
                        @else
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded border text-center text-sm text-gray-500">
                                ⏳ Primero selecciona un consultorio
                            </div>
                        @endif
                    </div>

                    {{-- Col 3: Formulario de cita (solo visible si hay hora seleccionada) --}}
                    <div>
                        @if($start_datetime && $doctor_medicaloffice_id && $selected_date)
                            <div class="p-3 bg-white dark:bg-gray-800 rounded border">
                                <h3 class="font-semibold mb-3">4. Cita para {{ \Carbon\Carbon::parse($start_datetime)->format('d/m/Y H:i') }}</h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Paciente</label>
                                        <select wire:model.defer="patient_id" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                                            <option value="">-- Selecciona paciente --</option>
                                            @foreach(($availablePatients ?? []) as $id => $label)
                                                <option value="{{ $id }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('patient_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Tipo de consulta</label>
                                        <input wire:model.defer="consultation_type" type="text" placeholder="Ej: Consulta inicial" 
                                               class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                                    </div>
                                    {{-- Estado is intentionally hidden during creation; shown only when editing an existing appointment --}}
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Consulta (detalle)</label>
                                        <textarea wire:model.defer="consultation_notes" rows="4" 
                                                  class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" 
                                                  placeholder="Motivo de consulta, síntomas, etc."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Notas administrativas</label>
                                        <textarea wire:model.defer="notes" rows="2" 
                                                  class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" 
                                                  placeholder="Notas internas"></textarea>
                                    </div>
                                    <!-- Buttons moved to footer below the 3-column grid -->
                                </div>
                            </div>
                        @elseif($selected_date && $doctor_medicaloffice_id)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded border text-center text-sm text-gray-500">
                                ⏰ Selecciona una hora para completar la cita
                            </div>
                        @elseif($doctor_medicaloffice_id)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded border text-center text-sm text-gray-500">
                                📅 Selecciona una fecha
                            </div>
                        @else
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded border text-center text-sm text-gray-500">
                                ⏳ Comienza seleccionando un consultorio
                            </div>
                        @endif
                    </div>
                </div>
                <!-- Footer buttons common to both edit and create flows -->
                <div class="mt-4 border-t pt-3 flex justify-end gap-2">
                    <button wire:click="save" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Guardar</button>
                    <button wire:click="resetForm" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                </div>
            </div>
        @endif
    @endif

    {{-- Select2 removed per request; no select2 assets or styles are loaded here --}}

    @php $items = $appointments ?? ($items ?? collect()); @endphp

    <div class="w-full overflow-x-auto bg-transparent">
        <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
            <thead class="hidden md:table-header-group">
                <tr class="text-left"><th class="px-3 py-2">Paciente</th><th class="px-3 py-2">Doctor</th><th class="px-3 py-2">Horario</th><th class="px-3 py-2">Acciones</th></tr>
            </thead>
            <tbody class="md:table-row-group">
                @forelse($items as $item)
                    @php
                        $turnRow = $appointmentTurns[$item->id] ?? null;
                        $rowHighlight = ($turnRow === 1) ? 'bg-green-50 dark:bg-green-700' : 'bg-white dark:bg-gray-800';
                    @endphp
                    <tr class="block md:table-row mb-3 md:mb-0 {{ $rowHighlight }} rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                        <td class="px-3 py-1 text-gray-800 dark:text-gray-100">{{ $item->patient?->name ?? ($item->patient_id ? 'Paciente #' . $item->patient_id : '—') }}</td>
                        <td class="px-3 py-1">
                            <div class="text-sm text-gray-800 dark:text-gray-100">{{ $item->doctorMedicalOffice?->doctor?->user?->name ?? '—' }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                @php $office = $item->doctorMedicalOffice?->medicalOffice; @endphp
                                @if($office)
                                    <a href="{{ route('medical-offices.show', $office->id) }}?ajax=1" class="hover:underline consultorio-link" data-id="{{ $office->id }}">{{ $office->name }}</a>
                                @else
                                    —
                                @endif
                            </div>
                        </td>
                        <td class="px-3 py-1 text-gray-800 dark:text-gray-100">
                                    @php
                                        $turn = $appointmentTurns[$item->id] ?? null;
                                    @endphp
                                    @php $total = $appointmentTotals[$item->id] ?? null; @endphp
                                    <div class="font-semibold">@if($turn && $total) Cita #{{ $turn }} de {{ $total }} @elseif($turn) Cita #{{ $turn }} @else Cita @endif</div>
                                    <div class="text-sm text-gray-700 dark:text-gray-200 mt-1">{{ $item->start_datetime ? \Carbon\Carbon::parse($item->start_datetime)->format('d/m/Y') : '—' }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $item->start_datetime ? \Carbon\Carbon::parse($item->start_datetime)->format('H:i') : '—' }}@if($item->end_datetime) - {{ \Carbon\Carbon::parse($item->end_datetime)->format('H:i') }}@endif</div>
                                </td>
                        <td class="px-3 py-1">
                            <div class="flex gap-2">
                                <button wire:click="edit({{ $item->id ?? 0 }})" class="px-2 py-1 bg-yellow-400 text-white rounded">Editar</button>
                                @if(method_exists($item, 'trashed') ? $item->trashed() : false)
                                    <button onclick="confirmAction('restore', {{ $item->id ?? 0 }})" class="px-2 py-1 bg-green-600 text-white rounded">Restaurar</button>
                                @else
                                    <button onclick="confirmAction('delete', {{ $item->id ?? 0 }})" class="px-2 py-1 bg-red-600 text-white rounded">Eliminar</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="p-4 text-center text-gray-500">No hay registros</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3">@if(method_exists($items, 'links')) {{ $items->links() }} @endif</div>

    </div> <!-- end #page-root -->

    <style>
        /* Blur ~60% effect for background when modal open */
        .page-blur {
            filter: blur(6px);
            opacity: 0.6;
            transition: filter 180ms ease, opacity 180ms ease;
            pointer-events: none;
        }
    </style>

    @include('livewire._modals')
</div>

<!-- schedule_id is passed directly in wire:click to selectTimeSlot -->

<script>
    function showConsultorioSearch() {
        var input = document.getElementById('consultorio-search');
        if (input) {
            input.classList.remove('hidden');
            input.focus();
        }
    }
</script>

<script>
    function toggleDoctorDropdown(e) {
        e.preventDefault();
        var root = document.getElementById('doctor-dropdown-root');
        var dropdown = document.getElementById('doctor-dropdown');
        var input = document.getElementById('doctor-dropdown-input');
        if (!dropdown) return;
        if (dropdown.classList.contains('hidden')) {
            dropdown.classList.remove('hidden');
            if (input) input.focus();
            document.addEventListener('click', outsideDoctorDropdown);
        } else {
            dropdown.classList.add('hidden');
            document.removeEventListener('click', outsideDoctorDropdown);
        }
    }

    function outsideDoctorDropdown(ev) {
        var root = document.getElementById('doctor-dropdown-root');
        if (!root) return;
        if (!root.contains(ev.target)) {
            var dropdown = document.getElementById('doctor-dropdown');
            if (dropdown && !dropdown.classList.contains('hidden')) dropdown.classList.add('hidden');
            document.removeEventListener('click', outsideDoctorDropdown);
        }
    }

    function selectDoctorPlace(id) {
        console.log('[diag] selectDoctorPlace called', id);
        // try to set the visible selected label immediately by finding the option element's text
        try {
            var selEl = document.querySelector('#doctor-dropdown [onclick="selectDoctorPlace(' + id + ')"]');
            if (!selEl) {
                // try without quotes (numeric)
                selEl = document.querySelector('#doctor-dropdown [onclick="selectDoctorPlace(' + id + ')"]');
            }
            if (selEl) {
                var selLabel = selEl.textContent && selEl.textContent.trim();
                if (selLabel) {
                    var display = document.getElementById('doctor-dropdown-selected');
                    if (display) display.textContent = selLabel;
                }
            }
        } catch(e) { console.warn('[diag] error setting immediate label', e); }

        // call Livewire method to set selection
        if (window.Livewire) {
            try {
                if (typeof Livewire.emit === 'function') {
                    Livewire.emit('doctorPlaceSelected', id);
                } else {
                    // attempt to find the nearest Livewire component and call the method directly
                    var ddRoot = document.getElementById('doctor-dropdown-root');
                    var livewireEl = (ddRoot && typeof ddRoot.closest === 'function') ? ddRoot.closest('[wire\\:id]') : document.querySelector('[wire\\:id]');
                    var cid = null;
                    if (livewireEl) cid = livewireEl.getAttribute('wire:id') || livewireEl.getAttribute('wire\\:id');
                    if (cid && typeof Livewire.find === 'function') {
                        try {
                            var comp = Livewire.find(cid);
                            if (comp && typeof comp.call === 'function') {
                                comp.call('doctorPlaceSelected', id);
                            } else if (comp && typeof comp.set === 'function') {
                                // last resort: set a property that the component listens to
                                comp.set('doctor_medicaloffice_id', id);
                            } else {
                                throw new Error('Livewire component methods unavailable');
                            }
                        } catch (e) {
                            console.warn('[diag] Livewire.find call failed', e);
                            // fallback to ajax label fetch below
                            throw e;
                        }
                    } else {
                        throw new Error('Livewire.emit and Livewire.find not available');
                    }
                }
            } catch (e) {
                // fallback: get label for id via simple endpoint
                fetch('/ajax/doctor-places/search?q=' + encodeURIComponent(id))
                    .then(r => r.json())
                    .then(data => {
                        var sel = document.getElementById('doctor-dropdown-selected');
                        if (sel) sel.textContent = (data[0] && data[0].label) || ('Consultorio #' + id);
                    }).catch(()=>{});
            }
        } else {
            // fallback: get label for id via simple endpoint
            fetch('/ajax/doctor-places/search?q=' + encodeURIComponent(id))
                .then(r => r.json())
                .then(data => {
                    var sel = document.getElementById('doctor-dropdown-selected');
                    if (sel) sel.textContent = (data[0] && data[0].label) || ('Consultorio #' + id);
                }).catch(()=>{});
        }
        // also set hidden input value (robust fallback for Livewire syncing)
        try {
            var hidden = document.getElementById('doctor-medicaloffice-hidden');
            if (hidden) {
                hidden.value = id;
                hidden.dispatchEvent(new Event('input', { bubbles: true }));
                // also dispatch change event for compatibility
                hidden.dispatchEvent(new Event('change', { bubbles: true }));
                // diagnostic: log value after a short tick
                setTimeout(function(){
                    try { console.log('[diag] hidden input value after dispatch =', hidden.value); } catch(e){}
                }, 50);
            }
        } catch(e){ console.warn('[diag] hidden input dispatch failed', e); }

        // close dropdown after a short delay so Livewire has time to pick up the input event
        setTimeout(function(){
            var dropdown = document.getElementById('doctor-dropdown');
            if (dropdown) dropdown.classList.add('hidden');
        }, 120);
    }

    function doctorDropdownInputChanged(val) {
        var dropdown = document.getElementById('doctor-dropdown');
        var input = document.getElementById('doctor-dropdown-input');
        if (!dropdown || !input) return;
        console.log('[diag] doctorDropdownInputChanged value=', val);
    // toggle clear button visibility
    try { var clearBtn = document.getElementById('doctor-dropdown-clear'); if (clearBtn) clearBtn.classList.toggle('hidden', !(val && val.length > 0)); } catch(e){}

    if (val && val.trim().length >= 3) {
            dropdown.classList.remove('hidden');
            console.log('[diag] show dropdown (3+ chars)');

            // Populate dropdown client-side immediately via AJAX so the dropdown doesn't rely
            // on Livewire to re-render its contents (which may close the dropdown).
            fetch('/ajax/doctor-places/search?q=' + encodeURIComponent(val))
                .then(r => r.json())
                .then(data => {
                    if (typeof populateDoctorDropdownFromJson === 'function') populateDoctorDropdownFromJson(data);
                }).catch(e => console.error('[diag] fetch error', e));

            if (window.Livewire) {
                try {
                    // prefer the Livewire component closest to the dropdown root so we don't target
                    // unrelated components (eg. navigation-menu) which causes PublicPropertyNotFound
                    var ddRoot = document.getElementById('doctor-dropdown-root');
                    var livewireEl = null;
                    if (ddRoot && typeof ddRoot.closest === 'function') {
                        livewireEl = ddRoot.closest('[wire\\:id]');
                    }
                    // fallback to any on the page if closest didn't find one
                    if (!livewireEl) livewireEl = document.querySelector('[wire\\:id]');
                    if (livewireEl) {
                        try {
                            if (ddRoot) ddRoot.dataset.keepopen = '1';
                        } catch(e){}
                        // Use event-based sync instead of direct .set to avoid targeting component ids
                        try {
                            Livewire.emit('consultorioSearchUpdated', val);
                            console.log('[diag] Livewire.emit consultorioSearchUpdated', val);
                        } catch(e) {
                            console.warn('[diag] Livewire.emit failed', e);
                        }
                    }
                } catch (e) { console.warn('[diag] Livewire set failed', e); }
            }
        } else {
            // if input emptied, clear the filter and repopulate default list
            if (!val || val.trim().length === 0) {
                console.log('[diag] input empty - clearing filter');
                // clear client-side list to recent/default
                fetch('/ajax/doctor-places/search')
                    .then(r => r.json())
                    .then(data => { if (typeof populateDoctorDropdownFromJson === 'function') populateDoctorDropdownFromJson(data); })
                    .catch(e => console.error('[diag] fetch error', e));
                // inform Livewire to clear its search term
                try { Livewire.emit('consultorioSearchUpdated', ''); } catch(e){}
                // ensure clear button hidden
                try { var clearBtn2 = document.getElementById('doctor-dropdown-clear'); if (clearBtn2) clearBtn2.classList.add('hidden'); } catch(e){}
                dropdown.classList.remove('hidden');
                return;
            }
            console.log('[diag] not enough chars to search');
        }
    }

    function clearDoctorDropdownFilter() {
        var input = document.getElementById('doctor-dropdown-input');
        if (input) input.value = '';
        // repopulate default items
        fetch('/ajax/doctor-places/search')
            .then(r => r.json())
            .then(data => { if (typeof populateDoctorDropdownFromJson === 'function') populateDoctorDropdownFromJson(data); })
            .catch(e => console.error('[diag] fetch error', e));
        // emit to Livewire to clear server-side term
        try { Livewire.emit('consultorioSearchUpdated', ''); } catch(e){}
        // hide clear button
        try { var clearBtn = document.getElementById('doctor-dropdown-clear'); if (clearBtn) clearBtn.classList.add('hidden'); } catch(e){}
        // ensure dropdown visible
        var dropdown = document.getElementById('doctor-dropdown'); if (dropdown) dropdown.classList.remove('hidden');
    }
</script>

<script>
    function populateDoctorDropdownFromJson(items) {
        var container = document.querySelector('#doctor-dropdown .max-h-48');
        if (!container) return;
        container.innerHTML = '';
        if (!items || items.length === 0) {
            container.innerHTML = '<div class="p-3 text-sm text-gray-500">No se encontraron coincidencias.</div>';
            return;
        }
        items.forEach(function(it){
            var el = document.createElement('div');
            el.className = 'p-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer';
            el.textContent = it.label;
            el.onclick = function(){ selectDoctorPlace(it.id); };
            container.appendChild(el);
        });
    }
</script>

<script>
    // Livewire debug hooks - wait for Livewire to be available before attaching hooks
    (function(){
        var waited = 0;
        var maxWait = 2000; // ms
        var interval = 100; // ms
        var handle = setInterval(function(){
            if (window.Livewire) {
                clearInterval(handle);
                try {
                    if (typeof Livewire.hook === 'function') {
                        Livewire.hook('message.sent', (message, component) => {
                            try {
                                var ddRoot = document.getElementById('doctor-dropdown-root');
                                if (ddRoot && ddRoot.dataset && ddRoot.dataset.keepopen) {
                                    // store the expected message/component id so we can match on processed
                                    var expected = null;
                                    if (message && message.fingerprint && message.fingerprint.id) expected = message.fingerprint.id;
                                    else if (message && message.component) expected = message.component;
                                    else if (component && component.__instance && component.__instance.id) expected = component.__instance.id;
                                    if (expected) {
                                        ddRoot.dataset.expectedCid = expected;
                                        console.log('[diag] stored expectedCid for dropdown reopen', expected);
                                    }
                                }
                            } catch(e) { console.warn('[diag] message.sent hook error', e); }
                        });
                        Livewire.hook('message.failed', (message, component) => {
                            console.error('[diag] Livewire message.failed', message);
                        });
                        Livewire.hook('message.processed', (message, component) => {
                                try {
                                // If our dropdown root requested to stay open, and this processed message
                                // belongs to the same message/component we stored earlier, reopen the dropdown.
                                var ddRoot = document.getElementById('doctor-dropdown-root');
                                if (!ddRoot) return;
                                if (!ddRoot.dataset || !ddRoot.dataset.keepopen) return;
                                // find the id the sent hook stored (if any)
                                var expectedCid = ddRoot.dataset.expectedCid || null;
                                // derive the processed message id
                                var targetCid = null;
                                if (message && message.fingerprint && message.fingerprint.id) targetCid = message.fingerprint.id;
                                else if (message && message.component) targetCid = message.component;
                                else if (component && component.__instance && component.__instance.id) targetCid = component.__instance.id;
                                // if expectedCid exists, require a match; otherwise fall back to closest element match
                                var livewireEl = ddRoot.closest('[wire\\:id]') || document.querySelector('[wire\\:id]');
                                if (!livewireEl) return;
                                var cid = livewireEl.getAttribute('wire:id') || livewireEl.getAttribute('wire\\:id');
                                if (!cid) return;
                                if (expectedCid) {
                                    if (String(expectedCid) !== String(targetCid) && String(expectedCid) !== String(cid)) {
                                        // not the message we're expecting
                                        console.log('[diag] skipping reopen; expected', expectedCid, 'got', targetCid, 'componentCid', cid);
                                        // clear stored expected to avoid future false matches
                                        try { delete ddRoot.dataset.expectedCid; } catch(e) { ddRoot.removeAttribute('data-expected-cid'); }
                                        return;
                                    }
                                } else {
                                    // no expected stored; require that processed message corresponds to this element's cid when possible
                                    if (targetCid && String(targetCid) !== String(cid)) return;
                                }
                                // reopen dropdown
                                var dropdown = document.getElementById('doctor-dropdown');
                                if (dropdown) {
                                    dropdown.classList.remove('hidden');
                                    // clear markers after reopening
                                    try { delete ddRoot.dataset.keepopen; delete ddRoot.dataset.expectedCid; } catch(e) { ddRoot.removeAttribute('data-keepopen'); ddRoot.removeAttribute('data-expected-cid'); }
                                    var input = document.getElementById('doctor-dropdown-input'); if (input) input.focus();
                                }
                            } catch (e) {
                                console.warn('[diag] error reopening dropdown', e);
                            }
                        });
                    } else if (typeof Livewire.on === 'function') {
                        Livewire.on('refreshComponent', payload => console.log('[diag] Livewire event refreshComponent', payload));
                    }
                    console.log('[diag] Livewire hooks attached');
                } catch (e) {
                    console.error('[diag] Livewire hooks error', e);
                }
                return;
            }
            waited += interval;
            if (waited >= maxWait) {
                clearInterval(handle);
                console.warn('[diag] Livewire not found on window after ' + maxWait + 'ms');
            }
        }, interval);
    })();
</script>
