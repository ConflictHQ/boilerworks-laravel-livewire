<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
        <a href="{{ route('products.index') }}" class="text-sm text-zinc-400 hover:text-white">Back to Products</a>
    </div>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-6 space-y-4">
        <div>
            <span class="text-sm text-zinc-400">Category:</span>
            <span class="ml-2">{{ $product->category?->name ?? '-' }}</span>
        </div>
        <div>
            <span class="text-sm text-zinc-400">Price:</span>
            <span class="ml-2">${{ number_format($product->price, 2) }}</span>
        </div>
        <div>
            <span class="text-sm text-zinc-400">Status:</span>
            <span class="ml-2 inline-flex px-2 py-1 rounded-full text-xs {{ $product->status === 'active' ? 'bg-emerald-900/50 text-emerald-300' : 'bg-zinc-700 text-zinc-400' }}">{{ ucfirst($product->status) }}</span>
        </div>
        @if($product->description)
        <div>
            <span class="text-sm text-zinc-400">Description:</span>
            <p class="mt-1">{{ $product->description }}</p>
        </div>
        @endif
    </div>
</x-app-layout>
