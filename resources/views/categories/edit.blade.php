<x-app-layout>
    <h1 class="text-2xl font-bold mb-6">Edit Category</h1>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-6">
        <form method="POST" action="{{ route('categories.update', $category) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label for="name" class="block text-sm text-zinc-400 mb-1">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" required class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">
                @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="description" class="block text-sm text-zinc-400 mb-1">Description</label>
                <textarea name="description" id="description" rows="3" class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">{{ old('description', $category->description) }}</textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <a href="{{ route('categories.show', $category) }}" class="rounded-lg border border-zinc-600 px-4 py-2 text-sm text-zinc-400 hover:text-white">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">Update Category</button>
            </div>
        </form>
    </div>
</x-app-layout>
