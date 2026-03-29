<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Categories</h1>
        @can('create', App\Models\Category::class)
        <a href="{{ route('categories.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">New Category</a>
        @endcan
    </div>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-900">
                <tr>
                    <th class="px-6 py-3 text-left text-zinc-400">Name</th>
                    <th class="px-6 py-3 text-left text-zinc-400">Items</th>
                    <th class="px-6 py-3 text-left text-zinc-400">Description</th>
                    <th class="px-6 py-3 text-right text-zinc-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
                @foreach($categories as $category)
                <tr class="hover:bg-zinc-750">
                    <td class="px-6 py-4"><a href="{{ route('categories.show', $category) }}" class="text-indigo-400 hover:text-indigo-300">{{ $category->name }}</a></td>
                    <td class="px-6 py-4 text-zinc-400">{{ $category->items_count }}</td>
                    <td class="px-6 py-4 text-zinc-400">{{ Str::limit($category->description, 60) }}</td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="{{ route('categories.edit', $category) }}" class="text-zinc-400 hover:text-white text-xs">Edit</a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 text-xs" onclick="return confirm('Delete this category?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $categories->links() }}</div>
</x-app-layout>
