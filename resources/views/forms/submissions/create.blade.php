<x-app-layout>
    <h1 class="text-2xl font-bold mb-6">Submit: {{ $form->name }}</h1>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-6">
        <form method="POST" action="{{ route('forms.submissions.store', $form) }}" class="space-y-4">
            @csrf
            @foreach($form->schema['fields'] ?? [] as $field)
            <div>
                <label for="data_{{ $field['name'] }}" class="block text-sm text-zinc-400 mb-1">{{ $field['label'] }}{{ ($field['required'] ?? false) ? ' *' : '' }}</label>
                @if(in_array($field['type'], ['text', 'email', 'number', 'date']))
                <input type="{{ $field['type'] }}" name="data[{{ $field['name'] }}]" id="data_{{ $field['name'] }}" value="{{ old('data.'.$field['name']) }}" {{ ($field['required'] ?? false) ? 'required' : '' }} placeholder="{{ $field['placeholder'] ?? '' }}" class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">
                @elseif($field['type'] === 'textarea')
                <textarea name="data[{{ $field['name'] }}]" id="data_{{ $field['name'] }}" rows="3" {{ ($field['required'] ?? false) ? 'required' : '' }} placeholder="{{ $field['placeholder'] ?? '' }}" class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">{{ old('data.'.$field['name']) }}</textarea>
                @elseif($field['type'] === 'select')
                <select name="data[{{ $field['name'] }}]" id="data_{{ $field['name'] }}" {{ ($field['required'] ?? false) ? 'required' : '' }} class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">
                    <option value="">-- Select --</option>
                    @foreach($field['options'] ?? [] as $opt)
                    <option value="{{ $opt['value'] }}" {{ old('data.'.$field['name']) === $opt['value'] ? 'selected' : '' }}>{{ $opt['label'] }}</option>
                    @endforeach
                </select>
                @elseif($field['type'] === 'radio')
                <div class="space-y-2">
                    @foreach($field['options'] ?? [] as $opt)
                    <label class="flex items-center space-x-2 text-sm">
                        <input type="radio" name="data[{{ $field['name'] }}]" value="{{ $opt['value'] }}" {{ old('data.'.$field['name']) === $opt['value'] ? 'checked' : '' }} class="text-indigo-600">
                        <span>{{ $opt['label'] }}</span>
                    </label>
                    @endforeach
                </div>
                @elseif($field['type'] === 'checkbox')
                <label class="flex items-center space-x-2 text-sm">
                    <input type="checkbox" name="data[{{ $field['name'] }}]" value="1" {{ old('data.'.$field['name']) ? 'checked' : '' }} class="text-indigo-600">
                    <span>{{ $field['label'] }}</span>
                </label>
                @endif
            </div>
            @endforeach
            @error('data') <p class="text-red-400 text-sm">{{ $message }}</p> @enderror
            <div class="flex justify-end space-x-3">
                <a href="{{ route('forms.show', $form) }}" class="rounded-lg border border-zinc-600 px-4 py-2 text-sm text-zinc-400 hover:text-white">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">Submit</button>
            </div>
        </form>
    </div>
</x-app-layout>
