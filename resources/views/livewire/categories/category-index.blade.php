<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Categories</h1>
        <a href="{{ route('livewire.categories.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700" wire:navigate>New Category</a>
    </div>

    <div class="mb-4">
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Search categories..."
            class="w-full max-w-sm rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100 placeholder-zinc-500"
        >
    </div>

    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-900">
                <tr>
                    <th class="px-6 py-3 text-left text-zinc-400">Name</th>
                    <th class="px-6 py-3 text-left text-zinc-400">Products</th>
                    <th class="px-6 py-3 text-left text-zinc-400">Description</th>
                    <th class="px-6 py-3 text-right text-zinc-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
                @forelse($categories as $category)
                <tr class="hover:bg-zinc-750">
                    <td class="px-6 py-4">
                        <a href="{{ route('categories.show', $category) }}" class="text-indigo-400 hover:text-indigo-300">{{ $category->name }}</a>
                    </td>
                    <td class="px-6 py-4 text-zinc-400">{{ $category->products_count }}</td>
                    <td class="px-6 py-4 text-zinc-400">{{ Str::limit($category->description, 60) }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('livewire.categories.edit', $category) }}" class="text-zinc-400 hover:text-white text-xs" wire:navigate>Edit</a>
                        <button
                            wire:click="delete('{{ $category->uuid }}')"
                            wire:confirm="Delete this category?"
                            class="text-red-400 hover:text-red-300 text-xs"
                        >Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-8 text-center text-zinc-500">No categories found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $categories->links() }}</div>
</div>
