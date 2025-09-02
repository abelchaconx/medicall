<div>
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <div class="col-span-1 flex justify-center sm:justify-start">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center sm:text-left">PERMISOS ELIMINADOS</h2>
            </div>
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1 flex justify-center sm:justify-end">
                <a href="<?php echo e(route('permissions.index')); ?>" class="inline-flex items-center justify-center px-4 py-2 rounded h-10 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100">Volver a permisos</a>
            </div>
        </div>
    </div>

    <div class="bg-transparent">
        <div class="w-full overflow-x-auto">
            <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
                <thead class="hidden md:table-header-group">
                    <tr class="text-left">
                        <th class="px-3 py-2">ID</th>
                        <th class="px-3 py-2">Name</th>
                        <th class="px-3 py-2">Label</th>
                        <th class="px-3 py-2">Eliminado</th>
                        <th class="px-3 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody class="md:table-row-group">
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="block md:table-row mb-3 md:mb-0 bg-white dark:bg-gray-800 rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</span>
                                    <span class="font-semibold"><?php echo e($p->id); ?></span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block font-semibold"><?php echo e($p->id); ?></span>
                                </div>
                            </td>
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</span>
                                    <span class=""><?php echo e($p->name); ?></span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block"><?php echo e($p->name); ?></span>
                                </div>
                            </td>
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Label</span>
                                    <span class=""><?php echo e($p->label); ?></span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block"><?php echo e($p->label); ?></span>
                                </div>
                            </td>
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Eliminado</span>
                                    <span class="text-sm"><?php echo e($p->deleted_at?->diffForHumans()); ?></span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block text-sm"><?php echo e($p->deleted_at?->diffForHumans()); ?></span>
                                </div>
                            </td>
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="mt-2 md:mt-0">
                                    <div class="md:hidden flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500">Acciones</span>
                                        <div class="flex items-center space-x-2">
                                            <button onclick="confirmActionPermissions('restore', <?php echo e($p->id); ?>)" class="inline-flex items-center justify-center w-9 h-9 rounded-full text-white bg-gradient-to-r from-green-500 to-green-600">R</button>
                                        </div>
                                    </div>
                                    <div class="hidden md:flex md:flex-row md:items-center md:gap-2">
                                        <button onclick="confirmActionPermissions('restore', <?php echo e($p->id); ?>)" class="w-full md:w-auto text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-green-500 to-green-600">Restaurar</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">No hay permisos eliminados</td>
                        </tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <div class="p-3">
            <?php echo e($permissions->links()); ?>

        </div>
    </div>

    <!--[if BLOCK]><![endif]--><?php if(session()->has('toast')): ?>
        <script>window.dispatchEvent(new CustomEvent('showToast',{detail:<?php echo json_encode(session('toast'), 15, 512) ?>}));</script>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Toast container -->
    <div x-data="{ show:false, message:'', color:'bg-green-500', timer:null, open(payloadOrType, maybeMessage=''){ let type='green'; let message=''; if(payloadOrType && typeof payloadOrType === 'string'){ const s = payloadOrType.trim(); if((s.startsWith('{')&& s.endsWith('}'))||(s.startsWith('[')&&s.endsWith(']'))){ try{ payloadOrType = JSON.parse(payloadOrType);}catch(e){}}} if(payloadOrType && typeof payloadOrType==='object'){ const candidate = payloadOrType.detail && typeof payloadOrType.detail === 'object' ? payloadOrType.detail : payloadOrType; type = candidate.type || candidate[0] || candidate.status || 'green'; message = candidate.message || candidate[1] || candidate.text || ''; } else{ type = payloadOrType || 'green'; message = maybeMessage || ''; } type=(type||'green').toString(); message=(message||'').toString(); this.message = message; if(type === 'red') this.color='bg-red-500'; else if(type === 'orange') this.color='bg-orange-500'; else this.color='bg-green-500'; this.show=true; clearTimeout(this.timer); this.timer=setTimeout(()=> this.show=false,3500);} }"
         x-init="window.addEventListener('toast', e => open(e.detail)); window.addEventListener('showToast', e => open(e.detail)); if(window.Livewire && typeof Livewire.on === 'function'){ Livewire.on('toast', (...args) => open(...args)); Livewire.on('showToast', (...args) => open(...args)); }"
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

    <!--[if BLOCK]><![endif]--><?php if(session()->has('toast')): ?>
        <script>window.dispatchEvent(new CustomEvent('showToast',{detail:<?php echo json_encode(session('toast'), 15, 512) ?>}));</script>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Confirmation modal for trashed permissions -->
<div id="confirm-modal-permissions" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm backdrop-filter p-4">
    <div id="confirm-panel-permissions" class="transform transition-opacity transition-transform duration-200 ease-out opacity-0 -translate-y-2 scale-95 bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-lg w-full overflow-hidden">
        <div class="p-6 text-center">
            <div id="confirm-icon-permissions" class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                <svg id="confirm-icon-svg-permissions" xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h3 id="confirm-title-permissions" class="text-xl font-semibold text-gray-900 dark:text-gray-100">¿Deseas restaurar este permiso?</h3>
            <div id="confirm-text-permissions" class="mt-2 text-sm text-gray-600 dark:text-gray-300">El permiso volverá a estar activo.</div>
        </div>
        <div class="px-6 pb-6 pt-0 bg-gray-50 dark:bg-gray-900 flex justify-center gap-3">
            <button id="confirm-cancel-permissions" class="px-4 py-2 rounded-md bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200">Cancelar</button>
            <button id="confirm-ok-permissions" class="px-4 py-2 rounded-md bg-green-600 text-white hover:bg-green-700 focus:outline-none">Confirmar</button>
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
            document.getElementById('confirm-title-permissions').textContent = '¿Deseas restaurar este permiso?';
            document.getElementById('confirm-text-permissions').textContent = 'El permiso volverá a estar activo.';
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
<?php /**PATH C:\laragon\www\medicall\resources\views/livewire/trashed-permissions.blade.php ENDPATH**/ ?>