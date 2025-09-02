<div>
            <!-- Top controls (3 columns: title | empty | actions) -->
            <div class="mb-4">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
                        <div class="col-span-1 flex justify-center sm:justify-start">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100 text-center sm:text-left">GESTIÓN DE USUARIOS</h2>
                    </div>

                    <div class="col-span-1 hidden sm:block">
                        <!-- spacer column (hidden on small screens) -->
                    </div>

                    <div class="col-span-1 flex justify-center sm:justify-end">
                        <div class="flex w-full gap-2">
                            <button wire:click="create" wire:loading.attr="disabled" wire:target="create" class="flex-1 w-full text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">Nuevo usuario</button>
                            <a href="<?php echo e(route('users.trashed')); ?>" class="flex-1 w-full inline-flex items-center justify-center bg-gray-700 text-white px-4 py-2 rounded h-10 hover:bg-gray-800 dark:bg-gray-600 dark:text-gray-100">Eliminados</a>
                        </div>
                    </div>
                </div>
            </div>

        <div class="mb-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
                <div class="col-span-1 hidden sm:block">
                    <!-- empty column for spacing/alignment (hidden on small screens) -->
                </div>

                    <div class="col-span-1">
                    <input wire:model.defer="search" type="text" placeholder="Buscar usuario..." class="border rounded px-3 py-2 w-full bg-white dark:bg-gray-900 dark:text-gray-200" />
                </div>

                <div class="col-span-1">
                    <div class="flex w-full items-center justify-center sm:justify-end gap-2">
                        <button wire:click="performSearch" wire:loading.attr="disabled" wire:target="performSearch" class="flex-1 w-full text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">Buscar</button>

                        <button wire:click="clearSearch" class="flex-1 w-full bg-gray-700 text-white px-4 py-2 rounded h-10 hover:bg-gray-800 dark:bg-gray-600 dark:text-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">Limpiar</button>
                    </div>
                    <div class="mt-2">
                        <span wire:loading wire:target="performSearch" class="text-sm text-gray-600 dark:text-gray-400">Buscando...</span>
                    </div>
                </div>
            </div>
        </div>
       
    <!-- Conditional form -->
    <!--[if BLOCK]><![endif]--><?php if($showForm): ?>
        <div class="p-4 border rounded bg-white dark:bg-gray-800 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre</label>
                    <input wire:model.defer="name" type="text" autocomplete="off" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                    <input wire:model.defer="email" type="email" autocomplete="off" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Password</label>
                    <input wire:model.defer="password" type="password" autocomplete="off" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-600 text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
            </div>

            <div class="mt-3 flex space-x-2">
                        <button wire:click="save" wire:loading.attr="disabled" wire:target="save" class="bg-green-600 text-white px-3 py-2 rounded disabled:opacity-50 disabled:cursor-not-allowed">Guardar</button>
                        <button wire:click="$set('showForm', false)" class="bg-gray-200 px-3 py-2 rounded disabled:opacity-50 disabled:cursor-not-allowed">Cancelar</button>
                <span wire:loading wire:target="save" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Guardando...</span>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Responsive users list: cards on mobile, table on desktop -->
    <div class="bg-transparent">
        <div class="w-full overflow-x-auto">
            <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
                <thead class="hidden md:table-header-group">
                    <tr class="text-left">
                        <th class="px-3 py-2">ID</th>
                        <th class="px-3 py-2">Nombre</th>
                        <th class="px-3 py-2">Email</th>
                        <th class="px-3 py-2">Rol</th>
                        <th class="px-3 py-2">Estado</th>
                        <th class="px-3 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody class="md:table-row-group">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="block md:table-row mb-3 md:mb-0 bg-white dark:bg-gray-800 rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">ID</span>
                                    <span class="font-semibold"><?php echo e($user->id); ?></span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block font-semibold"><?php echo e($user->id); ?></span>
                                </div>
                            </td>
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre</span>
                                    <span class=""><?php echo e($user->name); ?></span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block"><?php echo e($user->name); ?></span>
                                </div>
                            </td>
                            <td class="px-3 py-1 block md:table-cell align-top break-words">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</span>
                                    <span class="text-sm truncate max-w-[60%] text-left"><?php echo e($user->email); ?></span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block text-sm truncate"><?php echo e($user->email); ?></span>
                                </div>
                            </td>
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Rol</span>
                                    <span class=""><?php echo e($user->role?->name); ?></span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block"><?php echo e($user->role?->name); ?></span>
                                </div>
                            </td>
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="md:hidden flex justify-between items-start">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Estado</span>
                                    <span class=""><?php echo e($user->status ?? ($user->trashed() ? 'deleted' : 'active')); ?></span>
                                </div>
                                <div class="hidden md:block">
                                    <span class="block"><?php echo e($user->status ?? ($user->trashed() ? 'deleted' : 'active')); ?></span>
                                </div>
                            </td>
                            <td class="px-3 py-1 block md:table-cell align-top">
                                <div class="mt-2 md:mt-0">
                                    <div class="md:hidden flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-500">Acciones</span>
                                        <div class="flex items-center space-x-2">
                                            <button
                                                wire:click="edit(<?php echo e($user->id); ?>)"
                                                wire:loading.attr="disabled"
                                                wire:target="edit"
                                                class="inline-flex items-center justify-center w-9 h-9 rounded-full text-white bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                aria-label="Editar usuario <?php echo e($user->id); ?>"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h6M4 21v-7a4 4 0 014-4h3"/></svg>
                                            </button>

                                            <!--[if BLOCK]><![endif]--><?php if($user->trashed()): ?>
                                                <button
                                                    wire:click="toggleDelete(<?php echo e($user->id); ?>)"
                                                    wire:loading.attr="disabled"
                                                    wire:target="toggleDelete"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-full text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                    aria-label="Restaurar usuario <?php echo e($user->id); ?>"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4l3-3 4 4 5-5v11a2 2 0 01-2 2H5a2 2 0 01-2-2V10z"/></svg>
                                                </button>
                                            <?php else: ?>
                                                <button
                                                    wire:click="toggleDelete(<?php echo e($user->id); ?>)"
                                                    wire:loading.attr="disabled"
                                                    wire:target="toggleDelete"
                                                    class="inline-flex items-center justify-center w-9 h-9 rounded-full text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                    aria-label="Eliminar usuario <?php echo e($user->id); ?>"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4m-7 4h10"/></svg>
                                                </button>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </div>
                                    </div>

                                    <div class="hidden md:flex md:flex-row md:items-center md:gap-2">
                                        <button
                                            wire:click="edit(<?php echo e($user->id); ?>)"
                                            wire:loading.attr="disabled"
                                            wire:target="edit"
                                            class="w-full md:w-auto text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                            aria-label="Editar usuario <?php echo e($user->id); ?>"
                                        >
                                            Editar
                                        </button>

                                        <!--[if BLOCK]><![endif]--><?php if($user->trashed()): ?>
                                            <button
                                                wire:click="toggleDelete(<?php echo e($user->id); ?>)"
                                                wire:loading.attr="disabled"
                                                wire:target="toggleDelete"
                                                class="w-full md:w-auto text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                aria-label="Restaurar usuario <?php echo e($user->id); ?>"
                                            >
                                                Restaurar
                                            </button>
                                        <?php else: ?>
                                            <button
                                                wire:click="toggleDelete(<?php echo e($user->id); ?>)"
                                                wire:loading.attr="disabled"
                                                wire:target="toggleDelete"
                                                class="w-full md:w-auto text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                aria-label="Eliminar usuario <?php echo e($user->id); ?>"
                                            >
                                                Eliminar
                                            </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <div class="p-3">
            <?php echo e($users->links()); ?>

        </div>
    </div>

    <!-- Single Toast container (floating bottom-right) -->
    <div x-data="{
        show: false,
        message: '',
        color: 'bg-green-500',
        timer: null,
        open(payloadOrType, maybeMessage = '') {
            // Robustly accept these shapes:
            // - open({ type, message })
            // - open(type, message)
            // - open(JSON-string)
            // - event.detail that may itself contain `detail` or be an array-like
            let type = 'green';
            let message = '';

            // If we receive a JSON string, try to parse it
            if (payloadOrType && typeof payloadOrType === 'string') {
                const s = payloadOrType.trim();
                if ((s.startsWith('{') && s.endsWith('}')) || (s.startsWith('[') && s.endsWith(']'))) {
                    try { payloadOrType = JSON.parse(payloadOrType); } catch (e) { /* ignore */ }
                }
            }

            // If it's an object or array-like, read fields first
            if (payloadOrType && typeof payloadOrType === 'object') {
                // handle nested detail wrapper (e.detail.detail)
                const candidate = payloadOrType.detail && typeof payloadOrType.detail === 'object' ? payloadOrType.detail : payloadOrType;
                type = candidate.type || candidate[0] || candidate.status || 'green';
                message = candidate.message || candidate[1] || candidate.text || '';
            } else {
                // positional args: open(type, message)
                type = payloadOrType || 'green';
                message = maybeMessage || '';
            }

            // ensure strings
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

    <!--[if BLOCK]><![endif]--><?php if(session()->has('toast')): ?>
        <script>window.dispatchEvent(new CustomEvent('showToast',{detail:<?php echo json_encode(session('toast'), 15, 512) ?>}));</script>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>
<?php /**PATH C:\laragon\www\medicall\resources\views/livewire/users.blade.php ENDPATH**/ ?>