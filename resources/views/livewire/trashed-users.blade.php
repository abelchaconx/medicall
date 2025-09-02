<div>
    <!-- Responsive trashed users list: cards on mobile, table on desktop -->
    <div class="bg-transparent">
        <div class="w-full overflow-x-auto">
            <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
                <thead class="hidden md:table-header-group">
                    <tr class="text-left">
                        <th class="px-3 py-2">ID</th>
                        <th class="px-3 py-2">Nombre</th>
                        <th class="px-3 py-2">Email</th>
                        <th class="px-3 py-2">Eliminado</th>
                        <th class="px-3 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody class="md:table-row-group">
                    @forelse($users as $user)
                        <tr class="block md:table-row mb-3 md:mb-0 bg-white dark:bg-gray-800 rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</span>
                                    <span class="font-semibold">{{ $user->id }}</span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block font-semibold">{{ $user->id }}</span>
                                </div>
                            </td>

                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</span>
                                    <span class="">{{ $user->name }}</span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block">{{ $user->name }}</span>
                                </div>
                            </td>

                            <td class="px-3 py-1 block md:table-cell align-top break-words">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</span>
                                    <span class="text-sm truncate max-w-[60%] text-left">{{ $user->email }}</span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block text-sm truncate">{{ $user->email }}</span>
                                </div>
                            </td>

                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Eliminado</span>
                                    <span class="text-sm">{{ $user->deleted_at?->diffForHumans() }}</span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block text-sm">{{ $user->deleted_at?->diffForHumans() }}</span>
                                </div>
                            </td>

                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="mt-2 md:mt-0">
                                    <div class="md:hidden flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500">Acciones</span>
                                        <div class="flex items-center space-x-2">
                                            <button
                                                wire:click="restore({{ $user->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="restore"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-full text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                aria-label="Restaurar usuario {{ $user->id }}"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4l3-3 4 4 5-5v11a2 2 0 01-2 2H5a2 2 0 01-2-2V10z"/></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="hidden md:flex md:flex-row md:items-center md:gap-2">
                                        <button
                                            wire:click="restore({{ $user->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="restore"
                                            class="w-full md:w-auto text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                            aria-label="Restaurar usuario {{ $user->id }}"
                                        >
                                            Restaurar
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No hay usuarios eliminados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Toast container (same behavior as main users list) -->
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
                    try { payloadOrType = JSON.parse(payloadOrType); } catch (e) { }
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
</div>
