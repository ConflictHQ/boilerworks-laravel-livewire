<x-app-layout>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">{{ $form->name }}</h1>
        <div class="space-x-3">
            <a href="{{ route('forms.submissions.index', $form) }}" class="text-sm text-indigo-400 hover:text-indigo-300">Submissions ({{ $form->submissions_count }})</a>
            @if($form->status === App\Enums\FormStatus::Published)
            <a href="{{ route('forms.submissions.create', $form) }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">Submit</a>
            @endif
            <a href="{{ route('forms.index') }}" class="text-sm text-zinc-400 hover:text-white">Back</a>
        </div>
    </div>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-6 space-y-4">
        <div><span class="text-sm text-zinc-400">Slug:</span> <span class="ml-2">{{ $form->slug }}</span></div>
        <div><span class="text-sm text-zinc-400">Status:</span> <span class="ml-2">{{ ucfirst($form->status->value) }}</span></div>
        @if($form->description)<div><span class="text-sm text-zinc-400">Description:</span><p class="mt-1">{{ $form->description }}</p></div>@endif
        <div>
            <span class="text-sm text-zinc-400">Fields:</span>
            <ul class="mt-2 space-y-1">
                @foreach($form->schema['fields'] ?? [] as $field)
                <li class="text-sm">{{ $field['label'] }} <span class="text-zinc-500">({{ $field['type'] }}{{ ($field['required'] ?? false) ? ', required' : '' }})</span></li>
                @endforeach
            </ul>
        </div>
    </div>
</x-app-layout>
