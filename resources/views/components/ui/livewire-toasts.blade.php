<div
    x-data="toastComponent()"
    x-init="init()"
    class="fixed inset-0 flex items-end px-4 py-6 pointer-events-none sm:p-6 z-50"
    aria-live="polite"
>
    <div class="w-full flex flex-col items-center space-y-4 sm:items-end">
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="toast.show"
                x-transition
                x-cloak
                class="max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
            >
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="ml-3 w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="toast.type"></p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-300" x-text="toast.message"></p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button type="button" class="inline-flex text-gray-400 hover:text-gray-500" @click="removeById(toast.id)">
                                <span class="sr-only">Cerrar</span>
                                ✕
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>

<script>
// This script provides a safe fallback for `swal:confirm` events in case SweetAlert (Swal)
// is not loaded globally. The compiled JS already tries to use `Swal` when available,
// so here we only act as a graceful fallback to avoid missing-dependency errors.
(function(){
    function handleConfirmEvent(payload){
        var data = (payload && payload.detail) ? payload.detail : payload || {};
        var message = data.message || '¿Confirmar?';
        var id = data.id ?? null;

        // If SweetAlert is available, prefer it and let the existing compiled JS handle it.
        if (window.Swal) {
            return;
        }

        // Browser fallback using native confirm()
        try {
            if (confirm(message)) {
                try{
                    const el = document.querySelector('[wire\\:id]');
                    if (el && window.Livewire && Livewire.find) {
                        const c = el.getAttribute('wire:id') || el.getAttribute('wire\\:id');
                        if (c) { Livewire.find(c).call('deleteUser', id); return; }
                    }
                } catch(e){}

                if (window.Livewire && typeof Livewire.emit === 'function') {
                    Livewire.emit('deleteUser', id);
                } else if (window.dispatchEvent) {
                    window.dispatchEvent(new CustomEvent('swal:confirmed', { detail: { id } }));
                }
            }
        } catch(e) {
            // silence any errors in fallback
            console.warn('swal fallback error', e);
        }
    }

    if (window.Livewire && typeof Livewire.on === 'function') {
        Livewire.on('swal:confirm', handleConfirmEvent);
    } else {
        window.addEventListener('swal:confirm', handleConfirmEvent);
    }
})();
</script>
