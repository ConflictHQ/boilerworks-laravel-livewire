<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $workflow->name }}</h1>
        <a href="{{ route('workflows.index') }}" class="text-sm text-zinc-400 hover:text-white">Back</a>
    </div>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-6 space-y-4">
        <div><span class="text-sm text-zinc-400">Status:</span> <span class="ml-2">{{ ucfirst($workflow->status->value) }}</span></div>
        @if($workflow->description)<div><span class="text-sm text-zinc-400">Description:</span><p class="mt-1">{{ $workflow->description }}</p></div>@endif
        <div>
            <span class="text-sm text-zinc-400">States:</span>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach($workflow->states ?? [] as $state)
                <span class="inline-flex px-3 py-1 rounded-full text-xs bg-zinc-700 text-zinc-300">{{ $state['label'] }}{{ ($state['is_initial'] ?? false) ? ' (initial)' : '' }}{{ ($state['is_final'] ?? false) ? ' (final)' : '' }}</span>
                @endforeach
            </div>
        </div>
        <div>
            <span class="text-sm text-zinc-400">Transitions:</span>
            <ul class="mt-2 space-y-1 text-sm">
                @foreach($workflow->transitions ?? [] as $t)
                <li>{{ $t['from'] }} &rarr; {{ $t['to'] }} <span class="text-zinc-500">({{ $t['label'] }})</span></li>
                @endforeach
            </ul>
        </div>
    </div>
</x-app-layout>
