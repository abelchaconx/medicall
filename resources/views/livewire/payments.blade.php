<div>
    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <div class="col-span-1 flex justify-center sm:justify-start">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">GESTIÓN DE PAGOS</h2>
            </div>
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1 flex justify-center sm:justify-end">
                <div class="flex w-full gap-2">
                    <button wire:click="create" class="flex-1 text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-green-500 to-green-600">Nuevo pago</button>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 items-center">
            <div class="col-span-1 hidden sm:block"></div>
            <div class="col-span-1">
                <label for="search-payments" class="sr-only">Buscar</label>
                <input id="search-payments" wire:model.defer="search" type="text" placeholder="Buscar pago..." class="border rounded px-3 py-2 w-full bg-white dark:bg-gray-900 dark:text-gray-200" />
            </div>
            <div class="col-span-1">
                <div class="flex w-full items-center justify-center sm:justify-end gap-2">
                    <button wire:click="performSearch" class="flex-1 text-white px-4 py-2 rounded h-10 bg-gradient-to-r from-blue-500 to-blue-600">Buscar</button>
                    <button wire:click="clearSearch" class="flex-1 bg-gray-700 text-white px-4 py-2 rounded h-10">Limpiar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-transparent">
        <div class="w-full overflow-x-auto">
            <table class="min-w-full w-full table-auto text-gray-900 dark:text-gray-100">
                <thead class="hidden md:table-header-group"><tr class="text-left"><th class="px-3 py-2">ID</th><th class="px-3 py-2">Monto</th><th class="px-3 py-2">Acciones</th></tr></thead>
                <tbody class="md:table-row-group">
                    @foreach($payments as $payment)
                        <tr class="block md:table-row mb-3 md:mb-0 bg-white dark:bg-gray-800 rounded-lg md:rounded-none shadow-sm md:shadow-none overflow-hidden">
                            <td class="px-3 py-1">{{ $payment->id }}</td>
                            <td class="px-3 py-1">{{ $payment->amount ?? $payment->total ?? '—' }}</td>
                            <td class="px-3 py-1"><button wire:click="edit({{ $payment->id }})" class="px-3 py-1 rounded text-white bg-yellow-400">Editar</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">{{ $payments->links() }}</div>
    </div>

    @includeWhen(View::exists('livewire._partials.toast_confirm'), 'livewire._partials.toast_confirm')
</div>
