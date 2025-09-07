<div>
    <!-- Top controls (title | spacer | actions) -->
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <div class="col-span-1 flex justify-center sm:justify-start">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center sm:text-left">ESPECIALIDADES</h2>
            </div>

            <div class="col-span-1 hidden sm:block"></div>

            <div class="col-span-1 flex justify-center sm:justify-end">
                <div class="flex w-full gap-2">
                    <button wire:click="create" wire:loading.attr="disabled" wire:target="create" class="flex-1 w-full text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition">Nueva especialidad</button>
                    <a href="{{ route('specialties.trashed') }}" class="flex-1 w-full inline-flex items-center justify-center bg-gray-700 text-white px-4 py-2 rounded h-10 hover:bg-gray-800 dark:bg-gray-600 dark:text-gray-100">Eliminados</a>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
            <div class="col-span-1 hidden sm:block"></div>

            <div class="col-span-1">
                <input wire:model.defer="search" type="text" placeholder="Buscar especialidad..." class="border rounded px-3 py-2 w-full bg-white dark:bg-gray-900 dark:text-gray-200" />
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre</label>
                    <input wire:model.defer="name" type="text" autocomplete="off" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Descripción</label>
                    <input wire:model.defer="description" type="text" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    @error('description') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <!-- reserved for future fields like description -->
                </div>
                <div></div>
            </div>

            <div class="mt-3 flex space-x-2">
                <button wire:click="save" wire:loading.attr="disabled" wire:target="save" class="bg-green-600 text-white px-3 py-2 rounded">Guardar</button>
                <button wire:click="$set('showForm', false)" class="bg-gray-200 px-3 py-2 rounded">Cancelar</button>
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
                        <th class="px-3 py-2">Nombre</th>
                        <th class="px-3 py-2">Descripción</th>
                        <th class="px-3 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody class="md:table-row-group">
                    @foreach($specialties as $specialty)
                            <tr class="block md:table-row mb-3 md:mb-0 odd:bg-gray-50 even:bg-white dark:odd:bg-gray-800 dark:even:bg-gray-900 rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                            <td class="px-3 py-1">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</span>
                                    <span class="font-semibold">{{ $specialty->id }}</span>
                                </div>
                                <div class="hidden md:block"><span class="block font-semibold">{{ $specialty->id }}</span></div>
                            </td>

                            <td class="px-3 py-1">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</span>
                                    <span class="">{{ $specialty->name }}</span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="inline-flex items-center px-2 py-0.5 text-sm rounded text-black dark:text-white" style="background: {{ $specialty->color_translucent }}; border: 1px solid {{ $specialty->color }};">
                                        <span style="font-weight:600;">{{ $specialty->name }}</span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-3 py-1">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Descripción</span>
                                    <span class="text-sm truncate max-w-[60%]">{{ $specialty->description ?? '—' }}</span>
                                </div>
                                <div class="hidden md:block"><span class="block text-sm truncate">{{ $specialty->description ?? '—' }}</span></div>
                            </td>

                            <td class="px-3 py-1">
                                <div class="mt-2 md:mt-0">
                                    <div class="md:hidden flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500">Acciones</span>
                                        <div class="flex items-center space-x-2">
                                            <button wire:click="edit({{ $specialty->id }})" class="inline-flex items-center justify-center w-9 h-9 rounded-full text-white bg-gradient-to-r from-yellow-400 to-yellow-500">E</button>
                                        </div>
                                    </div>

                                    <div class="hidden md:flex md:flex-row md:items-center md:gap-2">
                                        <button wire:click="edit({{ $specialty->id }})" class="w-full md:w-auto text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-yellow-400 to-yellow-500">Editar</button>
                                        @if(method_exists($specialty, 'trashed') && $specialty->trashed())
                                            <button onclick="confirmAction('restore', {{ $specialty->id }})" class="w-full md:w-auto text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-green-500 to-green-600">Restaurar</button>
                                        @else
                                            <button onclick="confirmAction('delete', {{ $specialty->id }})" class="w-full md:w-auto text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-red-500 to-red-600">Eliminar</button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-3">{{ $specialties->links() }}</div>
    </div>

    <!-- Toast container (copied from users) -->
    <div x-data="{ show:false, message:'', color:'bg-green-500', timer:null, open(payloadOrType, maybeMessage=''){ let type='green'; let message=''; if(payloadOrType && typeof payloadOrType === 'string'){ const s = payloadOrType.trim(); if((s.startsWith('{')&& s.endsWith('}'))||(s.startsWith('[')&&s.endsWith(']'))){ try{ payloadOrType = JSON.parse(payloadOrType);}catch(e){}}} if(payloadOrType && typeof payloadOrType==='object'){ const candidate = payloadOrType.detail && typeof payloadOrType.detail === 'object' ? payloadOrType.detail : payloadOrType; type = candidate.type || candidate[0] || candidate.status || 'green'; message = candidate.message || candidate[1] || candidate.text || ''; } else{ type = payloadOrType || 'green'; message = maybeMessage || ''; } type=(type||'green').toString(); message=(message||'').toString(); this.message = message; if(type === 'red') this.color='bg-red-500'; else if(type === 'orange') this.color='bg-orange-500'; else this.color='bg-green-500'; this.show=true; clearTimeout(this.timer); this.timer=setTimeout(()=> this.show=false,3500);} }"
         x-init="window.addEventListener('toast', e => open(e.detail)); window.addEventListener('showToast', e => open(e.detail)); if(window.Livewire && typeof Livewire.on === 'function'){ // Livewire v3: toast events handled via addEventListener }"
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

    <!-- Confirmation modal (reused from users) -->
    <div id="confirm-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm backdrop-filter p-4">
        <div id="confirm-panel" class="transform transition-opacity transition-transform duration-200 ease-out opacity-0 -translate-y-2 scale-95 bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="p-6 text-center">
                <div id="confirm-icon" class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                    <svg id="confirm-icon-svg" xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <h3 id="confirm-title" class="text-xl font-semibold text-gray-900 dark:text-gray-100">¿Estás seguro?</h3>
                <div id="confirm-text" class="mt-2 text-sm text-gray-600 dark:text-gray-300">Esta acción no se puede deshacer.</div>
            </div>
            <div class="px-6 pb-6 pt-0 bg-gray-50 dark:bg-gray-900 flex justify-center gap-3">
                <button id="confirm-cancel" class="px-4 py-2 rounded-md bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">Cancelar</button>
                <button id="confirm-ok" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 focus:outline-none">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const modal = document.getElementById('confirm-modal');
            const panel = document.getElementById('confirm-panel');
            const btnOk = document.getElementById('confirm-ok');
            const btnCancel = document.getElementById('confirm-cancel');

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

            window.confirmAction = function(action, id) {
                pending = { action, id };
                if (action === 'delete') {
                    document.getElementById('confirm-title').textContent = '¿Deseas eliminar esta especialidad?';
                    document.getElementById('confirm-text').textContent = 'Se marcará como eliminada y no será accesible hasta restaurarse.';
                    document.getElementById('confirm-icon').className = 'mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 dark:bg-red-900 mb-4';
                    document.getElementById('confirm-icon-svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-7 4h10"/>';
                    btnOk.className = 'px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 focus:outline-none';
                } else if (action === 'restore') {
                    document.getElementById('confirm-title').textContent = '¿Deseas restaurar esta especialidad?';
                    document.getElementById('confirm-text').textContent = 'La especialidad volverá a estar activa.';
                    document.getElementById('confirm-icon').className = 'mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 dark:bg-green-900 mb-4';
                    document.getElementById('confirm-icon-svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
                    btnOk.className = 'px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 focus:outline-none';
                } else {
                    document.getElementById('confirm-title').textContent = 'Confirmar acción';
                    document.getElementById('confirm-text').textContent = '¿Estás seguro?';
                    btnOk.className = 'px-4 py-2 rounded-md bg-gray-800 text-white hover:bg-gray-900 focus:outline-none';
                }

                showModal();
                btnOk.focus();
            }

            btnCancel.addEventListener('click', hideModal);
            modal.addEventListener('click', function(e){ if (e.target === modal) hideModal(); });

            btnOk.addEventListener('click', function(){
                if (!pending) return hideModal();
                if (window.Livewire && typeof Livewire.emit === 'function') {
                    Livewire.emit('confirmAction', pending.action, pending.id);
                } else {
                    window.dispatchEvent(new CustomEvent('confirmAction', { detail: { action: pending.action, id: pending.id } }));
                }
                hideModal();
            });
        })();
    </script>
</div>
