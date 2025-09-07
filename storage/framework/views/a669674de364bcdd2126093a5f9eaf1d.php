<div>
    <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 items-center">
        <div><h2 class="text-lg font-semibold">CITAS ELIMINADAS</h2></div>
        <div class="flex justify-end"><a href="<?php echo e(route('appointments.index')); ?>" class="px-4 py-2 rounded bg-gray-700 text-white">Volver</a></div>
    </div>

    <?php $items = $appointments ?? ($items ?? collect()); ?>
    <div class="w-full overflow-x-auto">
        <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
            <thead class="hidden md:table-header-group"><tr><th class="px-3 py-2">ID</th><th class="px-3 py-2">Descripción</th><th class="px-3 py-2">Acciones</th></tr></thead>
            <tbody class="md:table-row-group">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="block md:table-row mb-3 md:mb-0 bg-white dark:bg-gray-800 rounded-lg">
                        <td class="px-3 py-1"><?php echo e($item->id ?? '—'); ?></td>
                        <td class="px-3 py-1"><?php echo e($item->description ?? ($item->name ?? '—')); ?></td>
                        <td class="px-3 py-1">
                            <button onclick="confirmAction('restore', <?php echo e($item->id ?? 0); ?>)" class="px-2 py-1 bg-green-600 text-white rounded">Restaurar</button>
                            <button onclick="confirmAction('forceDelete', <?php echo e($item->id ?? 0); ?>)" class="px-2 py-1 bg-red-600 text-white rounded">Eliminar permanentemente</button>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="3" class="p-4 text-center text-gray-500">No hay registros eliminados</td></tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    <div class="p-3"><!--[if BLOCK]><![endif]--><?php if(method_exists($items, 'links')): ?> <?php echo e($items->links()); ?> <?php endif; ?><!--[if ENDBLOCK]><![endif]--></div>

    <!-- reuse toast + modal from users via include if exists, else minimal fallback -->
    <?php echo $__env->renderWhen(View::exists('livewire._partials.toast_confirm'), 'livewire._partials.toast_confirm', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1])); ?>

</div>
<?php /**PATH C:\laragon\www\medicall\resources\views/livewire/trashed-appointments.blade.php ENDPATH**/ ?>