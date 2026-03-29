<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Items</h1>
        @can('create', App\Models\Item::class)
        <a href="{{ route('items.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">New Item</a>
        @endcan
    </div>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-900">
                <tr>
                    <th class="px-6 py-3 text-left text-zinc-400">Name</th>
                    <th class="px-6 py-3 text-left text-zinc-400">Category</th>
                    <th class="px-6 py-3 text-left text-zinc-400">Price</th>
                    <th class="px-6 py-3 text-left text-zinc-400">Status</th>
                    <th class="px-6 py-3 text-right text-zinc-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
                @foreach($items as $item)
                <tr class="hover:bg-zinc-750">
                    <td class="px-6 py-4"><a href="{{ route('items.show', $item) }}" class="text-indigo-400 hover:text-indigo-300">{{ $item->name }}</a></td>
                    <td class="px-6 py-4 text-zinc-400">{{ $item->category?->name ?? '-' }}</td>
                    <td class="px-6 py-4">${{ number_format($item->price, 2) }}</td>
                    <td class="px-6 py-4"><span class="inline-flex px-2 py-1 rounded-full text-xs {{ $item->status === 'active' ? 'bg-emerald-900/50 text-emerald-300' : 'bg-zinc-700 text-zinc-400' }}">{{ ucfirst($item->status) }}</span></td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('items.edit', $item) }}" class="text-zinc-400 hover:text-white text-xs">Edit</a>
                        <form method="POST" action="{{ route('items.destroy', $item) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 text-xs" onclick="return confirm('Delete this item?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
</x-app-layout>
