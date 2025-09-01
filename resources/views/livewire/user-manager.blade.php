<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold">Usuarios</h2>

        <div class="flex items-center space-x-3">
            <input wire:model.debounce.300ms="search" type="text" placeholder="Buscar..." class="border rounded px-3 py-2" />
            <button wire:click="create" class="bg-blue-600 text-white px-3 py-2 rounded">Nuevo usuario</button>
        </div>
    </div>

    <div x-data class="">
        @if($showForm)
            <div class="p-4 border rounded bg-white">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-sm font-medium">Nombre</label>
                        <input wire:model.defer="name" type="text" class="mt-1 block w-full border rounded px-2 py-1" />
                        @error('name') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Email</label>
                        <input wire:model.defer="email" type="email" class="mt-1 block w-full border rounded px-2 py-1" />
                        @error('email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Password</label>
                        <input wire:model.defer="password" type="password" class="mt-1 block w-full border rounded px-2 py-1" />
                        @error('password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-3 flex space-x-2">
                    <button wire:click="save" class="bg-green-600 text-white px-3 py-2 rounded">Guardar</button>
                    <button wire:click="$set('showForm', false)" class="bg-gray-200 px-3 py-2 rounded">Cancelar</button>
                </div>
            </div>
        @endif
    </div>

    <div class="bg-white border rounded">
        <table class="w-full table-auto">
            <thead>
                <tr class="text-left">
                    <th class="px-3 py-2">ID</th>
                    <th class="px-3 py-2">Nombre</th>
                    <th class="px-3 py-2">Email</th>
                    <th class="px-3 py-2">Rol</th>
                    <th class="px-3 py-2">Estado</th>
                    <th class="px-3 py-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="border-t">
                        <td class="px-3 py-2">{{ $user->id }}</td>
                        <td class="px-3 py-2">{{ $user->name }}</td>
                        <td class="px-3 py-2">{{ $user->email }}</td>
                        <td class="px-3 py-2">{{ $user->role?->name }}</td>
                        <td class="px-3 py-2">{{ $user->status ?? ($user->trashed() ? 'deleted' : 'active') }}</td>
                        <td class="px-3 py-2">
                            <button wire:click="edit({{ $user->id }})" class="text-sm text-blue-600 mr-2">Editar</button>
                            <button wire:click="toggleDelete({{ $user->id }})" class="text-sm text-red-600">{{ $user->trashed() ? 'Restaurar' : 'Eliminar' }}</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="p-3">
            {{ $users->links() }}
        </div>
    </div>
</div>
