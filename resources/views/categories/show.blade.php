<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $category->name }}</h1>
        <a href="{{ route('categories.index') }}" class="text-sm text-zinc-400 hover:text-white">Back to Categories</a>
    </div>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-6 space-y-4">
        <div>
            <span class="text-sm text-zinc-400">Items:</span>
            <span class="ml-2">{{ $category->items_count }}</span>
        </div>
        @if($category->description)
        <div>
            <span class="text-sm text-zinc-400">Description:</span>
            <p class="mt-1">{{ $category->description }}</p>
        </div>
        @endif
    </div>
</x-app-layout>
