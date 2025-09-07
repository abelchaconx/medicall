<div>
    <div class="mb-4 flex items-center justify-between"><h2 class="text-lg font-semibold">HORARIOS ELIMINADOS</h2><a href="{{ route('schedules.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded">Volver</a></div>
    @php $items = $schedules ?? ($items ?? collect()); @endphp
    <div class="w-full overflow-x-auto"><table class="min-w-full w-full table-auto"><thead class="hidden md:table-header-group"><tr><th>ID</th><th>Descripción</th><th>Acciones</th></tr></thead><tbody class="md:table-row-group">@forelse($items as $item)<tr class="block md:table-row mb-3 bg-white dark:bg-gray-800 rounded-lg"><td class="px-3 py-1">{{ $item->id ?? '—' }}</td><td class="px-3 py-1">{{ $item->description ?? ($item->name ?? '—') }}<div class="text-xs text-gray-500">{{ $item->doctorMedicalOffice?->doctor?->user?->name ?? $item->doctorMedicalOffice?->medicalOffice?->name ?? '' }}</div></td><td class="px-3 py-1"><button onclick="confirmAction('restore', {{ $item->id ?? 0 }})" class="px-2 py-1 bg-green-600 text-white rounded">Restaurar</button> <button onclick="confirmAction('forceDelete', {{ $item->id ?? 0 }})" class="px-2 py-1 bg-red-600 text-white rounded">Eliminar permanentemente</button></td></tr>@empty<tr><td colspan="3" class="p-4 text-center">No hay registros eliminados</td></tr>@endforelse</tbody></table></div>
    <div class="p-3">@if(method_exists($items, 'links')) {{ $items->links() }} @endif</div>

    @includeWhen(View::exists('livewire._partials.toast_confirm'), 'livewire._partials.toast_confirm')
</div>
