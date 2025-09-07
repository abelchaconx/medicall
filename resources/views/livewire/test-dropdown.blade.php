<div class="p-6 bg-white rounded-lg shadow">
    <h3 class="text-lg font-bold mb-4">Test Dropdown Cascada</h3>
    
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Departamento:</label>
            <select wire:model.live="city" class="mt-1 block w-full border rounded px-3 py-2">
                <option value="">-- Selecciona departamento --</option>
                @foreach($departamentos as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Valor actual: {{ $city ?: 'ninguno' }}</p>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700">Provincia:</label>
            <select wire:model="province" class="mt-1 block w-full border rounded px-3 py-2" @if(empty($city)) disabled @endif>
                <option value="">-- Selecciona provincia --</option>
                @foreach($provincias as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">
                Valor actual: {{ $province ?: 'ninguno' }} | 
                Provincias disponibles: {{ count($provincias) }}
            </p>
        </div>
        
        
    </div>
</div>
