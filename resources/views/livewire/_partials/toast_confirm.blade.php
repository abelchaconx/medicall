<!-- Shared toast + confirm partial (robust): used by livewire views to ensure consistent toasts and centered confirm modal -->
<div>
    <!-- Toast (bottom-right, animated, dark-mode aware) -->
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
                 // Livewire v3: toast events handled via addEventListener
                 // Livewire v3: showToast events handled via addEventListener
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

    <!-- Confirmation modal (centered, animated, dark-mode aware) -->
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
            const iconWrap = document.getElementById('confirm-icon');
            const iconSvg = document.getElementById('confirm-icon-svg');
            const titleEl = document.getElementById('confirm-title');
            const textEl = document.getElementById('confirm-text');
            const btnOk = document.getElementById('confirm-ok');
            const btnCancel = document.getElementById('confirm-cancel');

            let pending = null; // { action, id }

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

            window.confirmAction = function(action, id, count) {
                pending = { action, id, count };

                if (action === 'delete') {
                    titleEl.textContent = '¿Deseas eliminar este elemento?';
                    textEl.textContent = 'Se marcará como eliminado y no será accesible hasta restaurarse.';
                    iconWrap.className = 'mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 dark:bg-red-900 mb-4';
                    iconSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-7 4h10"/>';
                    iconSvg.className = 'h-10 w-10 text-red-600 dark:text-red-400';
                    btnOk.className = 'px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 focus:outline-none';
                } else if (action === 'restore') {
                    titleEl.textContent = '¿Deseas restaurar este elemento?';
                    textEl.textContent = 'El elemento volverá a estar activo.';
                    iconWrap.className = 'mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 dark:bg-green-900 mb-4';
                    iconSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>';
                    iconSvg.className = 'h-10 w-10 text-green-600 dark:text-green-400';
                    btnOk.className = 'px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 focus:outline-none';
                } else {
                    titleEl.textContent = 'Confirmar acción';
                    textEl.textContent = '¿Estás seguro?';
                    iconWrap.className = 'mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-gray-100 dark:bg-gray-800 mb-4';
                    iconSvg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01"/>';
                    iconSvg.className = 'h-10 w-10 text-gray-600 dark:text-gray-300';
                    btnOk.className = 'px-4 py-2 rounded-md bg-gray-800 text-white hover:bg-gray-900 focus:outline-none';
                }

                showModal();
                btnOk.focus();
            }

            function closeModal() {
                pending = null;
                hideModal();
            }

            btnCancel.addEventListener('click', closeModal);
            modal.addEventListener('click', function(e){ if (e.target === modal) closeModal(); });

            btnOk.addEventListener('click', function(){
                if (!pending) return closeModal();
                if (window.Livewire && typeof Livewire.emit === 'function') {
                    Livewire.emit('confirmAction', pending.action, pending.id);
                } else {
                    window.dispatchEvent(new CustomEvent('confirmAction', { detail: { action: pending.action, id: pending.id } }));
                }
                closeModal();
            });

            if (window.Livewire && typeof Livewire.on === 'function') {
                Livewire.on('openConfirm', (payload) => {
                    try {
                        const p = (payload && payload.detail) ? payload.detail : payload;
                        window.confirmAction(p.action, p.id, p.count);
                    } catch(e){}
                });
            }
        })();
    </script>
</div>
