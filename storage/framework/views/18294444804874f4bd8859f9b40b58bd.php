

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto p-4">
        <div class="bg-white dark:bg-gray-800 rounded p-4">
            <div class="flex items-center gap-2 mb-4">
                <button id="tab-schedules" class="px-4 py-2 bg-gray-700 text-white rounded">Horarios</button>
                <button id="tab-exceptions" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded">Excepciones</button>
            </div>

            <div id="panel-schedules">
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split(\App\Http\Livewire\Schedules::class);

$__html = app('livewire')->mount($__name, $__params, 'lw-472092786-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>

            <div id="panel-exceptions" class="hidden">
                <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split(\App\Http\Livewire\ScheduleExceptions::class);

$__html = app('livewire')->mount($__name, $__params, 'lw-472092786-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('tab-schedules').addEventListener('click', function(){
            document.getElementById('panel-schedules').classList.remove('hidden');
            document.getElementById('panel-exceptions').classList.add('hidden');
            this.classList.add('bg-gray-700','text-white');
            document.getElementById('tab-exceptions').classList.remove('bg-gray-700','text-white');
        });
        document.getElementById('tab-exceptions').addEventListener('click', function(){
            document.getElementById('panel-schedules').classList.add('hidden');
            document.getElementById('panel-exceptions').classList.remove('hidden');
            this.classList.add('bg-gray-700','text-white');
            document.getElementById('tab-schedules').classList.remove('bg-gray-700','text-white');
        });
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\medicall\resources\views/schedules/index.blade.php ENDPATH**/ ?>