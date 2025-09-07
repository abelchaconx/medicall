<div>
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Seleccionar horario</label>
                <select wire:model="selectedScheduleId" wire:change="selectSchedule($event.target.value)" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                    <option value="">-- Selecciona horario --</option>
                    @foreach($schedules as $s)
                        <option value="{{ $s->id }}">#{{ $s->id }} — {{ $s->description ?? 'Horario' }} ({{ $s->doctorMedicalOffice?->doctor?->user?->name ?? $s->doctorMedicalOffice?->medicalOffice?->name ?? '' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <div class="flex gap-2">
                    <input wire:model="date" type="date" class="border rounded px-2 py-1" />
                    <select wire:model.defer="type" class="border rounded px-2 py-1">
                        <option value="cancel">Cancelación</option>
                        <option value="extra">Extra</option>
                    </select>
                    <input wire:model="start_time" type="time" class="border rounded px-2 py-1" />
                    <input wire:model="end_time" type="time" class="border rounded px-2 py-1" />
                    <input wire:model="reason" placeholder="Motivo" class="border rounded px-2 py-1 flex-1" />
                    @error('date') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    @error('selectedScheduleId') <div class="text-xs text-red-600">{{ $message }}</div> @enderror
                    <button wire:click.prevent="save" class="px-4 py-2 bg-green-600 text-white rounded">Guardar</button>
                    <button wire:click.prevent="resetForm" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded">
        <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
            <thead class="hidden md:table-header-group"><tr class="text-left"><th class="px-3 py-2">ID</th><th class="px-3 py-2">Fecha</th><th class="px-3 py-2">Tipo</th><th class="px-3 py-2">Horas</th><th class="px-3 py-2">Motivo</th><th class="px-3 py-2">Acciones</th></tr></thead>
            <tbody class="md:table-row-group">
                @forelse($exceptions as $e)
                    <tr class="block md:table-row mb-3 md:mb-0 odd:bg-gray-50 even:bg-white dark:odd:bg-gray-800 dark:even:bg-gray-900 rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                        <td class="px-3 py-1">{{ $e->id }}</td>
                        <td class="px-3 py-1">{{ $e->date->toDateString() }}</td>
                        <td class="px-3 py-1">{{ $e->type }}</td>
                        <td class="px-3 py-1">{{ $e->start_time ?? '—' }} — {{ $e->end_time ?? '—' }}</td>
                        <td class="px-3 py-1">{{ $e->reason }}</td>
                        <td class="px-3 py-1">
                            <div class="flex gap-2">
                                <button wire:click.prevent="edit({{ $e->id }})" class="px-3 py-1 bg-yellow-400 rounded">Editar</button>
                                <button wire:click.prevent="delete({{ $e->id }})" class="px-3 py-1 bg-red-600 text-white rounded">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-4 text-center">Selecciona un horario para ver sus excepciones</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3">
        @if($exceptions instanceof \Illuminate\Contracts\Pagination\Paginator || $exceptions instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
            {{ $exceptions->links() }}
        @endif
    </div>

    @includeWhen(View::exists('livewire._partials.toast_confirm'), 'livewire._partials.toast_confirm')

</div>
