<div id="livewire-component-root">
    <div id="page-root">
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <div class="col-span-1 flex justify-center sm:justify-start">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">GESTIÓN DE CITAS</h2>
            </div>
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1 flex justify-center sm:justify-end">
                <div class="flex w-full gap-2">
                    <button wire:click="create" class="flex-1 text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-green-500 to-green-600">Nuevo cita</button>
                    <a href="<?php echo e(route('appointments.trashed')); ?>" class="flex-1 inline-flex items-center justify-center bg-gray-700 text-white px-4 py-2 rounded h-10">Eliminados</a>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1">
                <input wire:model.defer="search" type="text" placeholder="Buscar..." class="border rounded px-3 py-2 w-full bg-white dark:bg-gray-900 dark:text-gray-200" />
            </div>
            <div class="col-span-1 flex justify-center sm:justify-end">
                <div class="flex gap-2 w-full">
                    <button wire:click="performSearch" class="flex-1 text-white px-4 py-2 rounded h-10 bg-blue-600">Buscar</button>
                    <button wire:click="clearSearch" class="flex-1 bg-gray-700 text-white px-4 py-2 rounded h-10">Limpiar</button>
                </div>
            </div>
        </div>
    </div>

    

    <!--[if BLOCK]><![endif]--><?php if($showForm): ?>
        <!--[if BLOCK]><![endif]--><?php if($appointmentId): ?>
            
            <div class="p-4 border rounded bg-white dark:bg-gray-800 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Paciente</label>
                        <select wire:model.defer="patient_id" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                            <option value="">-- Selecciona paciente --</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = ($availablePatients ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['patient_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Consultorio</label>
                        <select wire:model="doctor_medicaloffice_id" wire:change="selectDoctor($event.target.value)" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                            <option value="">-- Selecciona consultorio --</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = ($availableDoctorMedicalOffices ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['doctor_medicaloffice_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                        <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Inicio</label>
                        <input wire:model.defer="start_datetime" type="datetime-local" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['start_datetime'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Fin</label>
                        <input wire:model.defer="end_datetime" type="datetime-local" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if($appointmentId): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Estado</label>
                        <select wire:model.defer="status" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                            <option value="">-- Selecciona estado --</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="atendido">Atendido</option>
                        </select>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Notas</label>
                        <textarea wire:model.defer="notes" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200"></textarea>
                    </div>
                </div>
                <!-- Buttons moved to footer below the 3-column grid -->
            </div>
        <?php else: ?>
            

            
            <div class="p-4 border rounded bg-white dark:bg-gray-800 mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    <div>
                        <label class="block text-sm font-medium mb-2">1. Buscar consultorio (doctor - consultorio)</label>
                        <select id="doctor-place-select" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" wire:model="doctor_medicaloffice_id" wire:change="selectDoctor($event.target.value)">
                            <option value="">-- Selecciona consultorio --</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = ($availableDoctorMedicalOffices ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($label); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>

                        



                        
                        <!--[if BLOCK]><![endif]--><?php if($doctor_medicaloffice_id): ?>
                            <div class="mt-3 p-2 bg-white dark:bg-gray-800 rounded border">
                                <h4 class="text-sm font-medium mb-2">2. Seleccionar fecha</h4>
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex gap-2">
                                        <button wire:click="prevMonth" class="px-2 py-1 border rounded">&lt;</button>
                                        <button wire:click="nextMonth" class="px-2 py-1 border rounded">&gt;</button>
                                    </div>
                                    <div class="font-semibold text-sm"><?php echo e(\Carbon\Carbon::parse($calendarMonth)->format('F Y')); ?></div>
                                </div>
                                <div class="grid grid-cols-7 gap-1 text-xs">
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="text-center font-medium"><?php echo e($d); ?></div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php
                                        $start = \Carbon\Carbon::parse($calendarMonth)->startOfMonth();
                                        $end = \Carbon\Carbon::parse($calendarMonth)->endOfMonth();
                                        $pad = ($start->isoWeekday() === 7) ? 6 : ($start->isoWeekday()-1);
                                        $days = [];
                                        for ($i=0;$i<$pad;$i++) $days[] = null;
                                        for ($d = $start->copy(); $d->lte($end); $d->addDay()) $days[] = $d->format('Y-m-d');
                                    ?>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <!--[if BLOCK]><![endif]--><?php if(!$day): ?>
                                            <div class="h-12 border rounded bg-gray-50 dark:bg-gray-700"></div>
                                        <?php else: ?>
                                            <?php
                                                    $isSelected = $selected_date === $day;
                                                    $dayClass = 'h-12 border rounded p-1 cursor-pointer text-xs ';
                                                    $info = $dailyAvailability[$day] ?? null;
                                                    if ($isSelected) {
                                                        $dayClass .= 'bg-blue-200 dark:bg-blue-800 border-blue-400';
                                                    } else {
                                                        // priority rules:
                                                        // 1) no schedules -> gray
                                                        // 2) available == 0 -> red
                                                        // 3) available/total < 0.5 -> yellow
                                                        // 4) booked > 0 -> green
                                                        // 5) otherwise green
                                                        if (! $info || ! ($info['hasSchedules'] ?? false)) {
                                                            $dayClass .= 'bg-gray-100 dark:bg-gray-800 border-gray-200 text-gray-700';
                                                        } else {
                                                            $available = $info['available'] ?? 0;
                                                            $total = $info['total'] ?? 0;
                                                            $booked = $info['booked'] ?? 0;

                                                            if ($total === 0) {
                                                                $dayClass .= 'bg-gray-100 dark:bg-gray-800 border-gray-200 text-gray-700';
                                                            } elseif ($available === 0) {
                                                                $dayClass .= 'bg-red-100 dark:bg-red-900 border-red-300 text-red-800';
                                                            } elseif ($total > 0 && ($available / $total) < 0.5) {
                                                                $dayClass .= 'bg-yellow-100 dark:bg-yellow-900 border-yellow-300 text-yellow-800';
                                                            } elseif ($booked > 0) {
                                                                $dayClass .= 'bg-green-100 dark:bg-green-900 border-green-300 text-green-800';
                                                            } else {
                                                                $dayClass .= 'bg-green-50 dark:bg-green-900 border-green-200';
                                                            }
                                                        }
                                                    }
                                                ?>
                                            <div class="<?php echo e($dayClass); ?>" wire:click="selectDate('<?php echo e($day); ?>')">
                                                <div><?php echo e(\Carbon\Carbon::parse($day)->format('j')); ?></div>
                                            </div>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="mt-3 p-4 bg-gray-50 dark:bg-gray-700 rounded border text-center text-sm text-gray-500">
                                ⏳ Selecciona un consultorio para ver el calendario
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    
                    <div>
                        <!--[if BLOCK]><![endif]--><?php if($selected_date && $doctor_medicaloffice_id): ?>
                            <div class="p-3 bg-white dark:bg-gray-800 rounded border">
                                <h3 class="font-semibold mb-2">3. Disponibles para <?php echo e(\Carbon\Carbon::parse($selected_date)->format('d/m/Y')); ?></h3>
                                <!--[if BLOCK]><![endif]--><?php if(empty($available_hours)): ?>
                                    <div class="text-sm text-gray-500">No hay horas disponibles para esta fecha.</div>
                                <?php else: ?>
                                    <div class="grid grid-cols-2 gap-2">
                                        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $available_hours; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <button wire:click="selectTimeSlot('<?php echo e($slot['start']); ?>', <?php echo e($slot['schedule_id'] ?? 'null'); ?>)"
                                                    class="px-2 py-1 bg-green-100 hover:bg-green-200 rounded text-sm transition-colors">
                                                <?php echo e($slot['start']); ?> - <?php echo e($slot['end']); ?>

                                            </button>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        <?php elseif($doctor_medicaloffice_id): ?>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded border text-center text-sm text-gray-500">
                                📅 Selecciona una fecha para ver las horas disponibles
                            </div>
                        <?php else: ?>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded border text-center text-sm text-gray-500">
                                ⏳ Primero selecciona un consultorio
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    
                    <div>
                        <!--[if BLOCK]><![endif]--><?php if($start_datetime && $doctor_medicaloffice_id && $selected_date): ?>
                            <div class="p-3 bg-white dark:bg-gray-800 rounded border">
                                <h3 class="font-semibold mb-3">4. Cita para <?php echo e(\Carbon\Carbon::parse($start_datetime)->format('d/m/Y H:i')); ?></h3>
                                <div class="space-y-3">
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Paciente</label>
                                        <select wire:model.defer="patient_id" class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                                            <option value="">-- Selecciona paciente --</option>
                                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = ($availablePatients ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($id); ?>"><?php echo e($label); ?></option>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                        </select>
                                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['patient_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-600"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Tipo de consulta</label>
                                        <input wire:model.defer="consultation_type" type="text" placeholder="Ej: Consulta inicial" 
                                               class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Consulta (detalle)</label>
                                        <textarea wire:model.defer="consultation_notes" rows="4" 
                                                  class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" 
                                                  placeholder="Motivo de consulta, síntomas, etc."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium mb-1">Notas administrativas</label>
                                        <textarea wire:model.defer="notes" rows="2" 
                                                  class="w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" 
                                                  placeholder="Notas internas"></textarea>
                                    </div>
                                    <!-- Buttons moved to footer below the 3-column grid -->
                                </div>
                            </div>
                        <?php elseif($selected_date && $doctor_medicaloffice_id): ?>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded border text-center text-sm text-gray-500">
                                ⏰ Selecciona una hora para completar la cita
                            </div>
                        <?php elseif($doctor_medicaloffice_id): ?>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded border text-center text-sm text-gray-500">
                                📅 Selecciona una fecha
                            </div>
                        <?php else: ?>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded border text-center text-sm text-gray-500">
                                ⏳ Comienza seleccionando un consultorio
                            </div>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
                <!-- Footer buttons common to both edit and create flows -->
                <div class="mt-4 border-t pt-3 flex justify-end gap-2">
                    <button wire:click="save" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Guardar</button>
                    <button wire:click="resetForm" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    

    <?php $items = $appointments ?? ($items ?? collect()); ?>

    <div class="w-full overflow-x-auto bg-transparent">
        <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
            <thead class="hidden md:table-header-group">
                <tr class="text-left"><th class="px-3 py-2">Paciente</th><th class="px-3 py-2">Doctor</th><th class="px-3 py-2">Horario</th><th class="px-3 py-2">Acciones</th></tr>
            </thead>
            <tbody class="md:table-row-group">
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="block md:table-row mb-3 md:mb-0 bg-white dark:bg-gray-800 rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                        <td class="px-3 py-1"><?php echo e($item->patient?->name ?? ($item->patient_id ? 'Paciente #' . $item->patient_id : '—')); ?></td>
                        <td class="px-3 py-1">
                            <div class="text-sm"><?php echo e($item->doctorMedicalOffice?->doctor?->user?->name ?? '—'); ?></div>
                            <div class="text-xs text-gray-500 mt-1">
                                <?php $office = $item->doctorMedicalOffice?->medicalOffice; ?>
                                <!--[if BLOCK]><![endif]--><?php if($office): ?>
                                    <a href="<?php echo e(route('medical-offices.show', $office->id)); ?>?ajax=1" class="hover:underline consultorio-link" data-id="<?php echo e($office->id); ?>"><?php echo e($office->name); ?></a>
                                <?php else: ?>
                                    —
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </td>
                        <td class="px-3 py-1"><?php echo e($item->start_datetime ? $item->start_datetime->format('d/m/Y H:i') : '—'); ?><!--[if BLOCK]><![endif]--><?php if($item->end_datetime): ?> - <?php echo e($item->end_datetime->format('H:i')); ?><?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            <div class="text-xs text-gray-500 mt-1"><!--[if BLOCK]><![endif]--><?php if($item->schedule): ?> Horario: <?php echo e($item->schedule->start_time); ?> - <?php echo e($item->schedule->end_time); ?> <?php endif; ?><!--[if ENDBLOCK]><![endif]--></div>
                        </td>
                        <td class="px-3 py-1">
                            <div class="flex gap-2">
                                <button wire:click="edit(<?php echo e($item->id ?? 0); ?>)" class="px-2 py-1 bg-yellow-400 text-white rounded">Editar</button>
                                <!--[if BLOCK]><![endif]--><?php if(method_exists($item, 'trashed') ? $item->trashed() : false): ?>
                                    <button onclick="confirmAction('restore', <?php echo e($item->id ?? 0); ?>)" class="px-2 py-1 bg-green-600 text-white rounded">Restaurar</button>
                                <?php else: ?>
                                    <button onclick="confirmAction('delete', <?php echo e($item->id ?? 0); ?>)" class="px-2 py-1 bg-red-600 text-white rounded">Eliminar</button>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="4" class="p-4 text-center text-gray-500">No hay registros</td></tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    <div class="p-3"><!--[if BLOCK]><![endif]--><?php if(method_exists($items, 'links')): ?> <?php echo e($items->links()); ?> <?php endif; ?><!--[if ENDBLOCK]><![endif]--></div>

    </div> <!-- end #page-root -->

    <style>
        /* Blur ~60% effect for background when modal open */
        .page-blur {
            filter: blur(6px);
            opacity: 0.6;
            transition: filter 180ms ease, opacity 180ms ease;
            pointer-events: none;
        }
    </style>

    <?php echo $__env->make('livewire._modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>

<!-- schedule_id is passed directly in wire:click to selectTimeSlot -->
<?php /**PATH C:\laragon\www\medicall\resources\views/livewire/appointments.blade.php ENDPATH**/ ?>