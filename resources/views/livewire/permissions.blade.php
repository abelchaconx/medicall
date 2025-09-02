<div>
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <div class="col-span-1 flex justify-center sm:justify-start">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center sm:text-left">GESTIÓN DE PERMISOS</h2>
            </div>
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1 flex justify-center sm:justify-end">
                <div class="flex w-full gap-2">
                    <button wire:click="create" class="flex-1 w-full text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700">Nuevo permiso</button>
                    <a href="{{ route('permissions.trashed') }}" class="flex-1 w-full text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 text-center">Eliminados</a>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1">
                <label for="search-permissions" class="sr-only">Buscar permiso</label>
                <input id="search-permissions" wire:model.defer="search" type="text" placeholder="Buscar permiso..." class="border rounded px-3 py-2 w-full bg-white dark:bg-gray-900 dark:text-gray-200" />
            </div>
            <div class="col-span-1">
                <div class="flex w-full items-center justify-center sm:justify-end gap-2">
                    <button wire:click="performSearch" wire:loading.attr="disabled" wire:target="performSearch" class="flex-1 w-full text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-blue-500 to-blue-600">Buscar</button>
                    <button wire:click="clearSearch" class="flex-1 w-full bg-gray-700 text-white px-4 py-2 rounded h-10">Limpiar</button>
                </div>
            </div>
        </div>
    </div>

    @if($showForm)
        <div class="p-4 border rounded bg-white dark:bg-gray-800 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label for="permission-name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Name</label>
                    <input id="permission-name" wire:model.defer="name" type="text" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="permission-label" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Label</label>
                    <input id="permission-label" wire:model.defer="label" type="text" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    @error('label') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="mt-3 flex space-x-2">
                <button wire:click="save" class="bg-green-600 text-white px-3 py-2 rounded">Guardar</button>
                <button wire:click="$set('showForm', false)" class="bg-gray-200 px-3 py-2 rounded">Cancelar</button>
            </div>
        </div>
    @endif

    <div class="bg-transparent">
        <div class="w-full overflow-x-auto">
            <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
                <thead class="hidden md:table-header-group">
                    <tr class="text-left">
                        <th class="px-3 py-2">ID</th>
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Label</th>
                        <th class="px-3 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody class="md:table-row-group">
                    @foreach($permissions as $p)
                        <tr class="block md:table-row mb-3 md:mb-0 bg-white dark:bg-gray-800 rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</span>
                                    <span class="font-semibold">{{ $p->id }}</span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block font-semibold">{{ $p->id }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</span>
                                    <span class="">{{ $p->name }}</span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block">{{ $p->name }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Label</span>
                                    <span class="">{{ $p->label }}</span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block">{{ $p->label }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="mt-2 md:mt-0">
                                    <div class="md:hidden flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500">Acciones</span>
                                        <div class="flex items-center space-x-2">
                                            <button onclick="confirmActionPermissions('delete', {{ $p->id }})" class="inline-flex items-center justify-center w-9 h-9 rounded-full text-white bg-gradient-to-r from-red-500 to-red-600">Del</button>
                                        </div>
                                    </div>

                                    <div class="hidden md:flex md:flex-row md:items-center md:gap-2">
                                        <button wire:click="edit({{ $p->id }})" class="w-full md:w-auto text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-yellow-400 to-yellow-500">Editar</button>
                                        <button onclick="confirmActionPermissions('delete', {{ $p->id }})" class="w-full md:w-auto text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-red-500 to-red-600">Eliminar</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-3">
            {{ $permissions->links() }}
        </div>
    </div>

    <!-- Single Toast container (floating bottom-right) -->
    <div x-data="{
        show: false,
        message: '',
        color: 'bg-green-500',
        timer: null,
        open(payloadOrType, maybeMessage = '') {
            let type = 'green';
            let message = '';

            if (payloadOrType && typeof payloadOrType === 'string') {
                const s = payloadOrType.trim();
                if ((s.startsWith('{') && s.endsWith('}')) || (s.startsWith('[') && s.endsWith(']'))) {
                    try { payloadOrType = JSON.parse(payloadOrType); } catch (e) { /* ignore */ }
                }
            }

            if (payloadOrType && typeof payloadOrType === 'object') {
                const candidate = payloadOrType.detail && typeof payloadOrType.detail === 'object' ? payloadOrType.detail : payloadOrType;
                type = candidate.type || candidate[0] || candidate.status || 'green';
                message = candidate.message || candidate[1] || candidate.text || '';
            } else {
                type = payloadOrType || 'green';
                message = maybeMessage || '';
            }

            type = (type || 'green').toString();
            message = (message || '').toString();

            this.message = message;
            if (type === 'red') this.color = 'bg-red-500';
            else if (type === 'orange') this.color = 'bg-orange-500';
            else this.color = 'bg-green-500';
            this.show = true;
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.show = false, 3500);
        }
    }"
         x-init="
             window.addEventListener('toast', e => open(e.detail));
             window.addEventListener('showToast', e => open(e.detail));
             if (window.Livewire && typeof Livewire.on === 'function') {
                 Livewire.on('toast', (...args) => open(...args));
                 Livewire.on('showToast', (...args) => open(...args));
             }
         "
         class="fixed bottom-6 right-6 flex items-end justify-end pointer-events-none z-50 px-4 sm:px-6" aria-live="polite">
        <div x-show="show" x-transition.opacity.duration.200ms class="pointer-events-auto">
            <div class="max-w-sm w-full bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 border border-gray-100 dark:border-gray-700 overflow-hidden flex items-center">
                <div :class="color + ' flex items-center justify-center w-12 h-12'" class="flex-shrink-0">
                    <svg x-show="color.includes('green')" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <svg x-show="color.includes('red')" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    <svg x-show="color.includes('orange')" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"/></svg>
                </div>

                <div class="px-4 py-3 flex-1 text-sm text-gray-900 dark:text-gray-100">
                    <div x-text="message"></div>
                </div>

                <div class="px-2">
                    <button @click="show = false; clearTimeout(timer)" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none">
                        <span class="sr-only">Cerrar</span>
                        &times;
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if(session()->has('toast'))
        <script>window.dispatchEvent(new CustomEvent('showToast',{detail:@json(session('toast'))}));</script>
    @endif

    <!-- Confirmation modal for permissions -->
<div id="confirm-modal-permissions" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm backdrop-filter p-4">
    <div id="confirm-panel-permissions" class="transform transition-opacity transition-transform duration-200 ease-out opacity-0 -translate-y-2 scale-95 bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-lg w-full overflow-hidden">
        <div class="p-6 text-center">
            <div id="confirm-icon-permissions" class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                <svg id="confirm-icon-svg-permissions" xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 id="confirm-title-permissions" class="text-xl font-semibold text-gray-900 dark:text-gray-100">¿Deseas eliminar este permiso?</h3>
            <div id="confirm-text-permissions" class="mt-2 text-sm text-gray-600 dark:text-gray-300">El permiso será eliminado.</div>
        </div>
        <div class="px-6 pb-6 pt-0 bg-gray-50 dark:bg-gray-900 flex justify-center gap-3">
            <button id="confirm-cancel-permissions" class="px-4 py-2 rounded-md bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">Cancelar</button>
            <button id="confirm-ok-permissions" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 focus:outline-none">Confirmar</button>
        </div>
    </div>
</div>

<script>
    (function(){
        const modal = document.getElementById('confirm-modal-permissions');
        const panel = document.getElementById('confirm-panel-permissions');
        const btnOk = document.getElementById('confirm-ok-permissions');
        const btnCancel = document.getElementById('confirm-cancel-permissions');

        let pending = null;

        function showModal() {
            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                panel.classList.remove('opacity-0','-translate-y-2','scale-95');
                panel.classList.add('opacity-100','translate-y-0','scale-100');
            });
        }

        function hideModal() {
            panel.classList.remove('opacity-100','translate-y-0','scale-100');
            panel.classList.add('opacity-0','-translate-y-2','scale-95');
            setTimeout(() => modal.classList.add('hidden'), 200);
        }

        window.confirmActionPermissions = function(action, id) {
            pending = { action, id };
            document.getElementById('confirm-title-permissions').textContent = action === 'restore' ? 'Restaurar permiso' : 'Eliminar permiso';
            document.getElementById('confirm-text-permissions').textContent = action === 'restore' ? 'El permiso volverá a estar activo.' : 'El permiso será eliminado.';
            showModal();
            btnOk.focus();
        }

        btnCancel.addEventListener('click', hideModal);
        modal.addEventListener('click', function(e){ if (e.target === modal) hideModal(); });

        btnOk.addEventListener('click', function(){
            if (!pending) return hideModal();
            if (window.Livewire && typeof Livewire.emit === 'function') {
                Livewire.emit('confirmActionPermissions', pending.action, pending.id);
            } else {
                window.dispatchEvent(new CustomEvent('confirmActionPermissions', { detail: { action: pending.action, id: pending.id } }));
            }
            hideModal();
        });
    })();
</script>
