<div>
    <div class="flex items-center gap-2 mb-4">
        <input wire:model.debounce.500ms="search" type="text" placeholder="Buscar..." class="border rounded px-3 py-2 w-full" />
        <select wire:model="perPage" class="border rounded px-3 py-2">
            <option>5</option>
            <option selected>10</option>
            <option>25</option>
            <option>50</option>
        </select>
    </div>

    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="min-w-full">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-2">#</th>
                    <th class="p-2">@click="" wire:click.prevent="sortBy('name')" class="cursor-pointer">Name</th>
                    <th class="p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr class="border-t">
                        <td class="p-2">{{ $row->id }}</td>
                        <td class="p-2">{{ $row->name ?? $row->title ?? $row->email ?? '-' }}</td>
                        <td class="p-2">{{-- actions --}}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="p-4">No records</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $rows->links() }}</div>
</div>
