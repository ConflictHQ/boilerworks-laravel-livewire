<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $workflow->name }} - Instances</h1>
        <div class="space-x-3">
            <form method="POST" action="{{ route('workflows.instances.store', $workflow) }}" class="inline">
                @csrf
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">New Instance</button>
            </form>
            <a href="{{ route('workflows.show', $workflow) }}" class="text-sm text-zinc-400 hover:text-white">Back</a>
        </div>
    </div>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-900">
                <tr>
                    <th class="px-6 py-3 text-left text-zinc-400">ID</th>
                    <th class="px-6 py-3 text-left text-zinc-400">Current State</th>
                    <th class="px-6 py-3 text-left text-zinc-400">Transitions</th>
                    <th class="px-6 py-3 text-left text-zinc-400">Created</th>
                    <th class="px-6 py-3 text-right text-zinc-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
                @foreach($instances as $instance)
                <tr>
                    <td class="px-6 py-4 text-zinc-400">{{ Str::limit($instance->uuid, 8, '') }}</td>
                    <td class="px-6 py-4"><span class="inline-flex px-2 py-1 rounded-full text-xs bg-zinc-700 text-zinc-300">{{ $instance->current_state }}</span></td>
                    <td class="px-6 py-4 text-zinc-400">{{ $instance->transitionLogs->count() }}</td>
                    <td class="px-6 py-4 text-zinc-400">{{ $instance->created_at->diffForHumans() }}</td>
                    <td class="px-6 py-4 text-right">
                        @php
                            $available = collect($workflow->transitions ?? [])->where('from', $instance->current_state);
                        @endphp
                        @foreach($available as $t)
                        <form method="POST" action="{{ route('workflows.instances.transition', [$workflow, $instance]) }}" class="inline">
                            @csrf
                            <input type="hidden" name="to_state" value="{{ $t['to'] }}">
                            <button type="submit" class="text-indigo-400 hover:text-indigo-300 text-xs mr-2">{{ $t['label'] }}</button>
                        </form>
                        @endforeach
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $instances->links() }}</div>
</x-app-layout>
