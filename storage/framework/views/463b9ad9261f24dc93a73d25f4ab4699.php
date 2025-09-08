<div>
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Seleccionar horario</label>
                <select wire:model="selectedScheduleId" wire:change="selectSchedule($event.target.value)" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                    <option value="">-- Selecciona horario --</option>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $schedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s->id); ?>">#<?php echo e($s->id); ?> — <?php echo e($s->description ?? 'Horario'); ?> (<?php echo e($s->doctorMedicalOffice?->doctor?->user?->name ?? $s->doctorMedicalOffice?->medicalOffice?->name ?? ''); ?>)</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </select>
            </div>
            <div class="col-span-2">
                <div class="flex gap-2">
                    <input wire:model="date" type="date" class="border rounded px-2 py-1" />
                    <select wire:model.defer="type" class="border rounded px-2 py-1">
                        <option value="cancel">Cancelación</option>
                        <option value="extra">Extra</option>
                    </select>
                    <input wire:model="start_time" type="time" class="border rounded px-2 py-1" />
                    <input wire:model="end_time" type="time" class="border rounded px-2 py-1" />
                    <input wire:model="reason" placeholder="Motivo" class="border rounded px-2 py-1 flex-1" />
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-xs text-red-600"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedScheduleId'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-xs text-red-600"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    <button wire:click.prevent="save" class="px-4 py-2 bg-green-600 text-white rounded">Guardar</button>
                    <button wire:click.prevent="resetForm" class="px-4 py-2 bg-gray-200 rounded">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded">
        <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
            <thead class="hidden md:table-header-group"><tr class="text-left"><th class="px-3 py-2">ID</th><th class="px-3 py-2">Fecha</th><th class="px-3 py-2">Tipo</th><th class="px-3 py-2">Horas</th><th class="px-3 py-2">Motivo</th><th class="px-3 py-2">Acciones</th></tr></thead>
            <tbody class="md:table-row-group">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $exceptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="block md:table-row mb-3 md:mb-0 odd:bg-gray-50 even:bg-white dark:odd:bg-gray-800 dark:even:bg-gray-900 rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                        <td class="px-3 py-1"><?php echo e($e->id); ?></td>
                        <td class="px-3 py-1"><?php echo e($e->date->toDateString()); ?></td>
                        <td class="px-3 py-1"><?php echo e($e->type); ?></td>
                        <td class="px-3 py-1"><?php echo e($e->start_time ?? '—'); ?> — <?php echo e($e->end_time ?? '—'); ?></td>
                        <td class="px-3 py-1"><?php echo e($e->reason); ?></td>
                        <td class="px-3 py-1">
                            <div class="flex gap-2">
                                <button wire:click.prevent="edit(<?php echo e($e->id); ?>)" class="px-3 py-1 bg-yellow-400 rounded">Editar</button>
                                <button wire:click.prevent="delete(<?php echo e($e->id); ?>)" class="px-3 py-1 bg-red-600 text-white rounded">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="p-4 text-center">Selecciona un horario para ver sus excepciones</td></tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    <div class="p-3">
        <!--[if BLOCK]><![endif]--><?php if($exceptions instanceof \Illuminate\Contracts\Pagination\Paginator || $exceptions instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator): ?>
            <?php echo e($exceptions->links()); ?>

        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <?php echo $__env->renderWhen(View::exists('livewire._partials.toast_confirm'), 'livewire._partials.toast_confirm', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>

</div>
<?php /**PATH C:\laragon\www\medicall\resources\views/livewire/schedule-exceptions.blade.php ENDPATH**/ ?>