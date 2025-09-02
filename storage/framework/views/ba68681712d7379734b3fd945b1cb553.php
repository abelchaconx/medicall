

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto p-4">
        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('trashed-roles', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-191886537-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\medicall\resources\views/roles/trashed.blade.php ENDPATH**/ ?>