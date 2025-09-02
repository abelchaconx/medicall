

<?php $__env->startSection('content'); ?>
<div class="container mx-auto p-4">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold dark:text-gray-100">USUARIOS ELIMINADOS</h1>
        <a href="<?php echo e(route('users.index')); ?>" class="text-sm text-blue-600 w-full md:w-auto text-sm px-3 py-1 rounded text-white bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 transition disabled:opacity-50 disabled:cursor-not-allowed">Volver a usuarios</a>
    </div>

    <?php if(session()->has('toast')): ?>
        <div class="mb-4 text-sm text-green-700"><?php echo e(session('toast.message')); ?></div>
    <?php endif; ?>

    <div>
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('trashed-users', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-2322142328-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\medicall\resources\views/users/trashed.blade.php ENDPATH**/ ?>