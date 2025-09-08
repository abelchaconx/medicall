<div>
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <div class="col-span-1 flex justify-center sm:justify-start">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">GESTIÓN DE PACIENTES</h2>
            </div>
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1 flex justify-center sm:justify-end">
                <div class="flex w-full gap-2">
                    <button wire:click="create" class="flex-1 text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-green-500 to-green-600">Nuevo paciente</button>
                    <a href="<?php echo e(route('patients.trashed')); ?>" class="flex-1 inline-flex items-center justify-center bg-gray-700 text-white px-4 py-2 rounded h-10">Eliminados</a>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1">
                <label for="search-patients" class="sr-only">Buscar</label>
                <input id="search-patients" wire:model.defer="search" type="text" placeholder="Buscar paciente..." class="border rounded px-3 py-2 w-full bg-white dark:bg-gray-900 dark:text-gray-200" />
            </div>
            <div class="col-span-1">
                <div class="flex w-full items-center justify-center sm:justify-end gap-2">
                    <button wire:click="performSearch" class="flex-1 text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-blue-500 to-blue-600">Buscar</button>
                    <button wire:click="clearSearch" class="flex-1 bg-gray-700 text-white px-4 py-2 rounded h-10">Limpiar</button>
                </div>
            </div>
        </div>
    </div>

    <!--[if BLOCK]><![endif]--><?php if($showForm): ?>
        <div class="p-4 border rounded bg-white dark:bg-gray-800 mb-4">
            <div x-data="{ create: <?php if ((object) ('createUser') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('createUser'->value()); ?>')<?php echo e('createUser'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('createUser'); ?>')<?php endif; ?> }" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!--[if BLOCK]><![endif]--><?php if (! ($patientId)): ?>
                    <div class="md:col-span-2 flex items-center gap-3">
                        <div class="flex items-center">
                            <input type="checkbox" id="createUser" wire:model="createUser" @change="if(create) { $wire.set('user_name', null); $wire.set('user_email', null); $wire.set('user_password', null); $wire.set('user_password_confirmation', null); } else { $wire.set('user_id', null); }" class="h-4 w-4" />
                            <label for="createUser" class="ml-2 text-sm text-gray-700 dark:text-gray-200">Usar usuario existente</label>
                        </div>
                    </div>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                <div class="md:col-span-2" x-show="!create" x-cloak style="display:none;">
                    <div class="mt-2 p-2 border rounded bg-gray-50 dark:bg-gray-900">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre Completo</label>
                                <input wire:model.defer="user_name" type="text" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Correo electrónico</label>
                                <input wire:model.defer="user_email" type="email" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Contraseña</label>
                                <input wire:model.defer="user_password" type="password" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Confirmar contraseña</label>
                                <input wire:model.defer="user_password_confirmation" type="password" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200"></label>
                        <div class="mt-1 flex items-center gap-3">
                            <!--[if BLOCK]><![endif]--><?php if (! ($patientId)): ?>
                                <div class="flex-1" x-show="create" x-cloak style="display:none;">
                                    <select wire:model.defer="user_id" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                                        <option value="">-- Asociar usuario --</option>
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = ($availableUsers ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($id); ?>"><?php echo e($label); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </select>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <!--[if BLOCK]><![endif]--><?php if($patientId): ?>
                                <div class="w-full mt-2" x-show="create" x-cloak style="display:none;">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Editar nombre de usuario</label>
                                            <input wire:model.defer="user_name" type="text" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Editar correo</label>
                                            <input wire:model.defer="user_email" type="email" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                    <!-- <p class="text-xs text-gray-500 mt-1">Opcional: si el usuario aún no existe, puedes crear la cuenta ahora marcando "Crear usuario".</p> -->
                </div>

                <div class="md:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Género</label>
                            <select wire:model.defer="gender" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                                <option value="">-- Selecciona género --</option>
                                <option value="male">Masculino</option>
                                <option value="female">Femenino</option>
                                <option value="other">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Fecha de nacimiento</label>
                            <input wire:model.defer="birthdate" type="date" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Teléfono</label>
                            <input wire:model.defer="phone" type="text" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Notas</label>
                    <textarea wire:model.defer="notes" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" rows="3"></textarea>
                </div>
            </div>
            </div>
            <div class="mt-3 flex flex-col sm:flex-row gap-2">
                <button wire:click="save" class="bg-green-600 text-white px-3 py-2 rounded w-full sm:w-auto">Guardar</button>
                <button wire:click="resetForm" class="bg-gray-200 px-3 py-2 rounded w-full sm:w-auto">Cancelar</button>
            </div>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <div class="bg-transparent">
        <div class="w-full overflow-x-auto">
            <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
                <thead class="hidden md:table-header-group">
                    <tr class="text-left">
                        <th class="px-3 py-2">ID</th>
                        <th class="px-3 py-2">Usuario</th>
                        <th class="px-3 py-2">Nacimiento</th>
                        <th class="px-3 py-2">Teléfono</th>
                        <th class="px-3 py-2">Notas</th>
                        <th class="px-3 py-2">Género</th>
                        <th class="px-3 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody class="md:table-row-group">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $patients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="block md:table-row mb-3 md:mb-0 bg-white dark:bg-gray-800 rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                            <td class="px-3 py-1"><?php echo e($patient->id); ?></td>
                            <td class="px-3 py-1"><?php echo e(optional($patient->user)->name ?? '—'); ?></td>
                            <td class="px-3 py-1"><?php echo e($patient->birthdate?->toDateString() ?? '—'); ?></td>
                            <td class="px-3 py-1"><?php echo e($patient->phone ?? '—'); ?></td>
                            <td class="px-3 py-1"><?php echo e(Str::limit($patient->notes, 60)); ?></td>
                            <td class="px-3 py-1"><?php echo e($patient->gender ? ucfirst($patient->gender) : '—'); ?></td>
                            <td class="px-3 py-1">
                                <div class="flex flex-col md:flex-row gap-2">
                                    <button wire:click="edit(<?php echo e($patient->id); ?>)" class="px-3 py-1 rounded text-white bg-yellow-400 w-full md:w-auto">Editar</button>
                                    <!--[if BLOCK]><![endif]--><?php if($patient->trashed()): ?>
                                        <button onclick="confirmAction('restore', <?php echo e($patient->id); ?>)" class="px-3 py-1 rounded text-white bg-green-600 w-full md:w-auto">Restaurar</button>
                                    <?php else: ?>
                                        <button onclick="confirmAction('delete', <?php echo e($patient->id); ?>)" class="px-3 py-1 rounded text-white bg-red-600 w-full md:w-auto">Eliminar</button>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>
        <div class="p-3"><?php echo e($patients->links()); ?></div>
    </div>

    <?php echo $__env->renderWhen(View::exists('livewire._partials.toast_confirm'), 'livewire._partials.toast_confirm', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>

    <!-- Quick-associate UI removed -->
</div>
<?php /**PATH C:\laragon\www\medicall\resources\views/livewire/patients.blade.php ENDPATH**/ ?>