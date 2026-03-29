<x-app-layout>
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-6">
            <h3 class="text-sm text-zinc-400 mb-1">Items</h3>
            <p class="text-3xl font-bold text-indigo-400">{{ $stats['items'] }}</p>
        </div>
        <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-6">
            <h3 class="text-sm text-zinc-400 mb-1">Categories</h3>
            <p class="text-3xl font-bold text-indigo-400">{{ $stats['categories'] }}</p>
        </div>
        <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-6">
            <h3 class="text-sm text-zinc-400 mb-1">Users</h3>
            <p class="text-3xl font-bold text-indigo-400">{{ $stats['users'] }}</p>
        </div>
    </div>
</x-app-layout>
