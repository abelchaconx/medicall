

<?php $__env->startSection('content'); ?>
    <div class="container mx-auto p-4">
        <div class="bg-white dark:bg-gray-800 rounded shadow p-4">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-4" aria-label="Tabs" id="doctors-tabs">
                    <button data-tab="doctors" class="tab-btn py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white focus:outline-none" aria-current="page">DOCTORES</button>
                    <button data-tab="specialties" class="tab-btn py-3 px-4 text-sm font-medium text-gray-700 dark:text-gray-300 border-b-2 border-transparent hover:text-gray-900 dark:hover:text-white focus:outline-none">ESPECIALIDADES</button>
                </nav>
            </div>

            <div class="mt-4">
                <div id="tab-doctors" class="tab-panel">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('doctors');

$__html = app('livewire')->mount($__name, $__params, 'lw-2071678589-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
                </div>
                <div id="tab-specialties" class="tab-panel hidden">
                    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('specialties');

$__html = app('livewire')->mount($__name, $__params, 'lw-2071678589-1', $__slots ?? [], get_defined_vars());

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
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function(){
        const tabs = document.querySelectorAll('#doctors-tabs .tab-btn');
        const panels = {
            doctors: document.getElementById('tab-doctors'),
            specialties: document.getElementById('tab-specialties')
        };

        function activate(name){
            tabs.forEach(t => {
                if (t.getAttribute('data-tab') === name) {
                    t.classList.add('border-blue-600','text-gray-900','dark:text-white');
                    t.classList.remove('border-transparent');
                } else {
                    t.classList.remove('border-blue-600','text-gray-900','dark:text-white');
                    t.classList.add('border-transparent');
                }
            });
            Object.keys(panels).forEach(k => {
                if (k === name) panels[k].classList.remove('hidden'); else panels[k].classList.add('hidden');
            });
        }

        tabs.forEach(t => t.addEventListener('click', function(){ activate(this.getAttribute('data-tab')) }));
        // default
        activate('doctors');
    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\medicall\resources\views/doctors/index.blade.php ENDPATH**/ ?>