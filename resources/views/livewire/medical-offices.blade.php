<div>
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <div class="col-span-1 flex justify-center sm:justify-start">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">GESTIÓN DE CONSULTORIOS MÉDICOS</h2>
            </div>
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1 flex justify-center sm:justify-end">
                <div class="flex w-full gap-2">
                    <button wire:click="create" class="flex-1 text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-green-500 to-green-600">Nuevo consultorio</button>
                    <a href="{{ route('medical-offices.trashed') }}" class="flex-1 inline-flex items-center justify-center bg-gray-700 text-white px-4 py-2 rounded h-10">Eliminados</a>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1">
                <label for="search-offices" class="sr-only">Buscar</label>
                <input id="search-offices" wire:model.defer="search" type="text" placeholder="Buscar consultorio..." class="border rounded px-3 py-2 w-full bg-white dark:bg-gray-900 dark:text-gray-200" />
            </div>
            <div class="col-span-1">
                <div class="flex w-full items-center justify-center sm:justify-end gap-2">
                    <button wire:click="performSearch" class="flex-1 text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-blue-500 to-blue-600">Buscar</button>
                    <button wire:click="clearSearch" class="flex-1 bg-gray-700 text-white px-4 py-2 rounded h-10">Limpiar</button>
                </div>
            </div>
        </div>
    </div>

    @if($showForm)
        <!-- Debug temporal -->
        @if(session('init_debug'))
            <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4">
                <strong>Init Debug:</strong> 
                Departamentos: {{ session('init_debug')['departamentos_count'] ?? 0 }}, 
                Provincias iniciales: {{ session('init_debug')['provincias_count'] ?? 0 }}, 
                Datos por depto: {{ session('init_debug')['provincias_por_depto_count'] ?? 0 }}
            </div>
        @endif
        
        @if(session('debug'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                <strong>Update Debug:</strong> 
                Ciudad: {{ session('debug')['city'] ?? 'vacía' }}, 
                Provincias: {{ session('debug')['provincias_count'] ?? 0 }}
                @if(!empty(session('debug')['provincias_keys']))
                    ({{ implode(', ', array_slice(session('debug')['provincias_keys'], 0, 3)) }}...)
                @endif
            </div>
        @endif
        
        <div class="p-4 border rounded bg-white dark:bg-gray-800 mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nombre de Consultorio</label>
                    <input wire:model.defer="name" type="text" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Dirección</label>
                    <input wire:model.defer="address_line" type="text" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    @error('address_line') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-2medium text-gray-700 dark:text-gray-200">Departamento</label>
                    <select wire:model.live="city" wire:change="changeDepartment" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200">
                        <option value="">-- Selecciona un departamento --</option>
                        @foreach($departamentos as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Departamento actual: {{ $city ?: 'ninguno' }}</p>
                    @error('city') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Teléfono</label>
                    <input wire:model.defer="phone" type="text" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    @error('phone') <p class="text-xs text-red-600">{{ $message }} </p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Otros</label>
                    <input wire:model.defer="otros" type="text" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    @error('otros') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Provincia</label>
                    <select wire:model="province" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" @if(empty($city)) disabled @endif>
                        <option value="">-- Selecciona una provincia --</option>
                        @foreach($provincias as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    @if(empty($city))
                        <p class="text-xs text-gray-500 mt-1">Primero selecciona un departamento</p>
                    @elseif(empty($provincias))
                        <p class="text-xs text-orange-500 mt-1">No hay provincias para este departamento</p>
                    @else
                        <p class="text-xs text-green-600 mt-1">{{ count($provincias) }} provincias disponibles</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">Estado: departamento={{ $city ?: 'vacío' }}, provincias={{ count($provincias) }}</p>
                    @error('province') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Latitud</label>
                    <input wire:model.defer="latitude" type="number" step="0.000001" min="-90" max="90" placeholder="Ej: -17.783327" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    @error('latitude') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Longitud</label>
                    <input wire:model.defer="longitude" type="number" step="0.000001" min="-180" max="180" placeholder="Ej: -63.182140" class="mt-1 block w-full border rounded px-2 py-1 bg-white dark:bg-gray-900 dark:text-gray-200" />
                    @error('longitude') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-3 flex space-x-2">
                <button wire:click="save" class="bg-green-600 text-white px-3 py-2 rounded">Guardar</button>
                <button wire:click="resetForm" class="bg-gray-200 px-3 py-2 rounded">Cancelar</button>
            </div>
        </div>
    @endif

    <!-- Script para debug -->
    <script>
        window.addEventListener('province-updated', event => {
            console.log('Provincias actualizadas:', event.detail.count);
        });
    </script>

    <div class="bg-transparent">
        @if($offices->count() > 0)
        <!-- Grid de tarjetas de consultorios -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($offices as $office)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <!-- Encabezado con nombre -->
                    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-3">
                        <h3 class="text-lg font-semibold truncate" title="{{ $office->name }}">{{ $office->name }}</h3>
                    </div>
                    
                    <!-- Información del consultorio -->
                    <div class="p-4">
                        <div class="space-y-2 mb-4">
                            <div class="flex items-start">
                                <svg class="w-4 h-4 mt-1 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <div class="text-sm">
                                    <p class="text-gray-700 dark:text-gray-300">{{ $office->address_line ?? 'Sin dirección' }}</p>
                                    <p class="text-gray-600 dark:text-gray-400">{{ $office->province ?? 'Sin provincia' }}, {{ $office->city ?? 'Sin departamento' }}</p>
                                </div>
                            </div>
                            
                            @if($office->phone)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                <span class="text-sm text-gray-700 dark:text-gray-300"><a target="_blank" href="https://wa.me/591{{ $office->phone }}">{{ $office->phone }}</a></span>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Mini mapa -->
                        @if($office->latitude && $office->longitude)
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Ubicación</h4>
                                <a href="https://www.google.com/maps?q={{ $office->latitude }},{{ $office->longitude }}" 
                                   target="_blank" 
                                   class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                    Ver en Google Maps →
                                </a>
                            </div>
                            <div class="w-full h-32 bg-gray-200 rounded overflow-hidden cursor-pointer">
                                <iframe
                                    width="100%"
                                    height="128"
                                    frameborder="0"
                                    scrolling="no"
                                    marginheight="0"
                                    marginwidth="0"
                                    src="https://maps.google.com/maps?q={{ $office->latitude }},{{ $office->longitude }}&hl=es&z=15&output=embed">
                                </iframe>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Coordenadas: {{ number_format($office->latitude, 6) }}, {{ number_format($office->longitude, 6) }}
                            </p>
                        </div>
                        @else
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ubicación</h4>
                            <div class="w-full h-32 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                                <p class="text-gray-500 dark:text-gray-400 text-sm">Sin coordenadas disponibles</p>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Lista de doctores asignados -->
                        @if($office->doctors && $office->doctors->isNotEmpty())
                            <div class="mt-3">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Doctores en este consultorio</h4>
                                <ul class="space-y-2">
                                    @foreach($office->doctors as $doc)
                                        <li class="flex flex-col md:flex-row md:items-center md:justify-between bg-gray-50 dark:bg-gray-900 p-2 rounded">
                                            <div class="flex items-start gap-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-semibold">{{ strtoupper(substr($doc->user?->name ?? 'DR',0,2)) }}</div>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $doc->user?->name ?? '—' }}</div>
                                                    <div class="text-xs mt-1">
                                                        @if($doc->specialties && $doc->specialties->isNotEmpty())
                                                            <div class="flex flex-wrap gap-2">
                                                                @foreach($doc->specialties as $s)
                                                                    <span class="text-xs px-2 py-0.5 rounded text-black dark:text-white" style="background: {{ $s->color_translucent }}; border: 1px solid {{ $s->color }};">{{ $s->name }}</span>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            —
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Botones de acción -->
                        <div class="flex gap-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                            <button wire:click="edit({{ $office->id }})" class="flex-1 px-3 py-2 rounded text-white bg-yellow-500 hover:bg-yellow-600 text-sm font-medium">
                                Editar
                            </button>
                            @if(method_exists($office, 'trashed') && $office->trashed())
                                <button onclick="confirmAction('restore', {{ $office->id }})" class="flex-1 px-3 py-2 rounded text-white bg-green-600 hover:bg-green-700 text-sm font-medium">
                                    Restaurar
                                </button>
                            @else
                                <button onclick="confirmAction('delete', {{ $office->id }})" class="flex-1 px-3 py-2 rounded text-white bg-red-600 hover:bg-red-700 text-sm font-medium">
                                    Eliminar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Paginación -->
        <div class="mt-6">{{ $offices->links() }}</div>
        @else
        <!-- Mensaje cuando no hay consultorios -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No hay consultorios médicos</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Comienza agregando un nuevo consultorio médico.</p>
            <div class="mt-6">
                <button wire:click="create" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Agregar Consultorio
                </button>
            </div>
        </div>
        @endif
    </div>

    @includeWhen(View::exists('livewire._partials.toast_confirm'), 'livewire._partials.toast_confirm')
</div>
