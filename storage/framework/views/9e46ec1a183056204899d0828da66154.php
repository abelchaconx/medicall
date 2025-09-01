<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Gestion de Usuarios</h2>

        <div class="flex items-center space-x-3">
            <input wire:model.defer="search" type="text" placeholder="Buscar..." class="border rounded px-3 py-2 bg-white dark:bg-gray-800 dark:text-gray-200" autocomplete="off" />
            <button wire:click="performSearch" wire:loading.attr="disabled" wire:target="performSearch" class="bg-gray-200 px-3 py-2 rounded">Buscar</button>
            <span wire:loading wire:target="performSearch" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Buscando...</span>
            <button wire:click="create" wire:loading.attr="disabled" wire:target="create" class="text-white px-3 py-2 rounded bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition">Nuevo usuario</button>
        </div>
    </div>

    <div x-data class="">
    <!--[if BLOCK]><![endif]--><?php if(!empty($showForm)): ?>
            <div class="p-4 border rounded bg-white dark:bg-gray-800">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium">Nombre</label>
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
                        <label class="block text-sm font-medium">Email</label>
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
                        <label class="block text-sm font-medium">Password</label>
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
                    <button wire:click="save" wire:loading.attr="disabled" wire:target="save" class="bg-green-600 text-white px-3 py-2 rounded">Guardar</button>
                    <span wire:loading wire:target="save" class="ml-2 text-sm text-gray-600 dark:text-gray-400">Guardando...</span>
                    <button wire:click="$set('showForm', false)" class="bg-gray-200 px-3 py-2 rounded">Cancelar</button>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <div class="bg-white border rounded dark:bg-gray-900 dark:border-gray-700">
        <table class="w-full table-auto text-gray-900 dark:text-gray-100">
            <thead>
                <tr class="text-left">
                    <th class="px-3 py-2">ID</th>
                    <th class="px-3 py-2">Nombre</th>
                    <th class="px-3 py-2">Email</th>
                    <th class="px-3 py-2">Rol</th>
                    <th class="px-3 py-2">Estado</th>
                    <th class="px-3 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-t dark:border-gray-700">
                        <td class="px-3 py-2"><?php echo e($user->id); ?></td>
                        <td class="px-3 py-2"><?php echo e($user->name); ?></td>
                        <td class="px-3 py-2"><?php echo e($user->email); ?></td>
                        <td class="px-3 py-2"><?php echo e($user->role?->name); ?></td>
                        <td class="px-3 py-2"><?php echo e($user->status ?? ($user->trashed() ? 'deleted' : 'active')); ?></td>
                        <td class="px-3 py-2">
                            

                            <!-- Edit button: yellow gradient -->
                            <button
                                wire:click="edit(<?php echo e($user->id); ?>)"
                                wire:loading.attr="disabled"
                                wire:target="edit"
                                class="text-sm mr-2 px-3 py-1 rounded text-white bg-gradient-to-r from-yellow-400 to-yellow-500 hover:from-yellow-500 hover:to-yellow-600 transition"
                                aria-label="Editar usuario <?php echo e($user->id); ?>"
                            >
                                Editar
                            </button>

                            <!-- Delete / Restore button: red for delete, green for restore -->
                            <!--[if BLOCK]><![endif]--><?php if($user->trashed()): ?>
                                <button
                                    wire:click="toggleDelete(<?php echo e($user->id); ?>)"
                                    wire:loading.attr="disabled"
                                    wire:target="toggleDelete"
                                    class="text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 transition"
                                    aria-label="Restaurar usuario <?php echo e($user->id); ?>"
                                >
                                    Restaurar
                                </button>
                            <?php else: ?>
                                <button
                                    wire:click="toggleDelete(<?php echo e($user->id); ?>)"
                                    wire:loading.attr="disabled"
                                    wire:target="toggleDelete"
                                    class="text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 transition"
                                    aria-label="Eliminar usuario <?php echo e($user->id); ?>"
                                >
                                    Eliminar
                                </button>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>

        <div class="p-3">
            <?php echo e($users->links()); ?>

        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\medicall\resources\views/livewire/users.blade.php ENDPATH**/ ?>