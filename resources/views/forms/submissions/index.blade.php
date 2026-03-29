<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $form->name }} - Submissions</h1>
        <a href="{{ route('forms.show', $form) }}" class="text-sm text-zinc-400 hover:text-white">Back to Form</a>
    </div>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-zinc-900">
                <tr>
                    <th class="px-6 py-3 text-left text-zinc-400">ID</th>
                    <th class="px-6 py-3 text-left text-zinc-400">Data</th>
                    <th class="px-6 py-3 text-left text-zinc-400">Submitted</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-700">
                @foreach($submissions as $submission)
                <tr>
                    <td class="px-6 py-4 text-zinc-400">{{ Str::limit($submission->uuid, 8, '') }}</td>
                    <td class="px-6 py-4 text-zinc-400 font-mono text-xs">{{ Str::limit(json_encode($submission->data), 100) }}</td>
                    <td class="px-6 py-4 text-zinc-400">{{ $submission->created_at->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $submissions->links() }}</div>
</x-app-layout>
