<div>
    <div class="mb-4 flex items-center justify-between"><h2 class="text-lg font-semibold">PRESCRIPCIONES ELIMINADAS</h2><a href="{{ route('prescriptions.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded">Volver</a></div>
    @php $items = $prescriptions ?? ($items ?? collect()); @endphp
    <div class="w-full overflow-x-auto"><table class="min-w-full w-full table-auto"><thead><tr><th>ID</th><th>Paciente</th><th>Acciones</th></tr></thead><tbody>@forelse($items as $item)<tr class="block md:table-row mb-3 bg-white dark:bg-gray-800 rounded-lg"><td class="px-3 py-1">{{ $item->id ?? '—' }}</td><td class="px-3 py-1">{{ $item->patient->name ?? ($item->name ?? '—') }}</td><td class="px-3 py-1"><button onclick="confirmAction('restore', {{ $item->id ?? 0 }})" class="px-2 py-1 bg-green-600 text-white rounded">Restaurar</button> <button onclick="confirmAction('forceDelete', {{ $item->id ?? 0 }})" class="px-2 py-1 bg-red-600 text-white rounded">Eliminar permanentemente</button></td></tr>@empty<tr><td colspan="3" class="p-4 text-center">No hay registros eliminados</td></tr>@endforelse</tbody></table></div>
    <div class="p-3">@if(method_exists($items,'links')) {{ $items->links() }} @endif</div>
    @includeWhen(View::exists('livewire._partials.toast_confirm'), 'livewire._partials.toast_confirm')
</div>
