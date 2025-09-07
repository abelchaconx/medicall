<div>
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <div class="col-span-1 flex justify-center sm:justify-start">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">ESPECIALIDADES ELIMINADAS</h2>
            </div>
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1 flex justify-center sm:justify-end">
                <a href="{{ route('specialties.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded h-10 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100">Volver a especialidades</a>
            </div>
        </div>
    </div>

    <div class="bg-transparent">
        <div class="w-full overflow-x-auto">
            <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
                <thead class="hidden md:table-header-group">
                    <tr class="text-left"><th class="px-3 py-2">ID</th><th class="px-3 py-2">Nombre</th><th class="px-3 py-2">Eliminado</th><th class="px-3 py-2">Acciones</th></tr>
                </thead>
                <tbody class="md:table-row-group">
                    @forelse($specialties as $s)
                        <tr class="block md:table-row mb-3 md:mb-0 bg-white dark:bg-gray-800 rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                            <td class="px-3 py-1">{{ $s->id }}</td>
                            <td class="px-3 py-1">{{ $s->name }}</td>
                            <td class="px-3 py-1">{{ $s->deleted_at?->diffForHumans() }}</td>
                            <td class="px-3 py-1">
                                <div class="flex gap-2">
                                    <button onclick="confirmActionSpecialties('restore', {{ $s->id }})" class="px-3 py-1 rounded text-white bg-green-500">Restaurar</button>
                                    <button onclick="confirmActionSpecialties('forceDelete', {{ $s->id }})" class="px-3 py-1 rounded text-white bg-red-600">Eliminar permanentemente</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">No hay especialidades eliminadas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3">{{ $specialties->links() }}</div>
    </div>

    <!-- Confirm modal -->
    <div id="confirm-modal-specialties" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-40 p-4">
        <div id="confirm-panel-specialties" class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-lg w-full p-6">
            <h3 id="confirm-title-specialties" class="text-xl font-semibold text-gray-900 dark:text-gray-100">¿Deseas restaurar esta especialidad?</h3>
            <p id="confirm-text-specialties" class="mt-2 text-sm text-gray-600 dark:text-gray-300">Esta acción restaurará la especialidad.</p>
            <div class="mt-4 flex justify-center gap-3">
                <button id="confirm-cancel-specialties" class="px-4 py-2 rounded bg-white border">Cancelar</button>
                <button id="confirm-ok-specialties" class="px-4 py-2 rounded bg-green-600 text-white">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const modal = document.getElementById('confirm-modal-specialties');
            const panel = document.getElementById('confirm-panel-specialties');
            const ok = document.getElementById('confirm-ok-specialties');
            const cancel = document.getElementById('confirm-cancel-specialties');
            let pending = null;

            function show() { modal.classList.remove('hidden'); }
            function hide(){ modal.classList.add('hidden'); }

            window.confirmActionSpecialties = function(action, id){
                pending = { action, id };
                if (action === 'forceDelete'){
                    document.getElementById('confirm-title-specialties').textContent = '¿Deseas eliminar permanentemente esta especialidad?';
                    document.getElementById('confirm-text-specialties').textContent = 'Esta acción no puede deshacerse.';
                } else {
                    document.getElementById('confirm-title-specialties').textContent = '¿Deseas restaurar esta especialidad?';
                    document.getElementById('confirm-text-specialties').textContent = 'Esta acción restaurará la especialidad.';
                }
                show();
            }

            cancel.addEventListener('click', hide);
            ok.addEventListener('click', function(){
                if (!pending) return hide();
                if (window.Livewire && typeof Livewire.emit === 'function') {
                    Livewire.emit('confirmActionSpecialties', pending.action, pending.id);
                } else {
                    window.dispatchEvent(new CustomEvent('confirmActionSpecialties', { detail: { action: pending.action, id: pending.id } }));
                }
                hide();
            });
        })();
    </script>
</div>
