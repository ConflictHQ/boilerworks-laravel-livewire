<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $item->name }}</h1>
        <a href="{{ route('items.index') }}" class="text-sm text-zinc-400 hover:text-white">Back to Items</a>
    </div>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-6 space-y-4">
        <div>
            <span class="text-sm text-zinc-400">Category:</span>
            <span class="ml-2">{{ $item->category?->name ?? '-' }}</span>
        </div>
        <div>
            <span class="text-sm text-zinc-400">Price:</span>
            <span class="ml-2">${{ number_format($item->price, 2) }}</span>
        </div>
        <div>
            <span class="text-sm text-zinc-400">Status:</span>
            <span class="ml-2 inline-flex px-2 py-1 rounded-full text-xs {{ $item->status === 'active' ? 'bg-emerald-900/50 text-emerald-300' : 'bg-zinc-700 text-zinc-400' }}">{{ ucfirst($item->status) }}</span>
        </div>
        @if($item->description)
        <div>
            <span class="text-sm text-zinc-400">Description:</span>
            <p class="mt-1">{{ $item->description }}</p>
        </div>
        @endif
    </div>
</x-app-layout>
