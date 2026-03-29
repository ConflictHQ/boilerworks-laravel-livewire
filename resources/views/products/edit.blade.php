<x-app-layout>
    <h1 class="text-2xl font-bold mb-6">Edit Product</h1>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-6">
        <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label for="name" class="block text-sm text-zinc-400 mb-1">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">
                @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="description" class="block text-sm text-zinc-400 mb-1">Description</label>
                <textarea name="description" id="description" rows="3" class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm text-zinc-400 mb-1">Price</label>
                    <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $product->price) }}" required class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">
                    @error('price') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm text-zinc-400 mb-1">Status</label>
                    <select name="status" id="status" class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">
                        <option value="active" {{ old('status', $product->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ old('status', $product->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
            </div>
            <div>
                <label for="category_id" class="block text-sm text-zinc-400 mb-1">Category</label>
                <select name="category_id" id="category_id" class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">
                    <option value="">-- None --</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <a href="{{ route('products.show', $product) }}" class="rounded-lg border border-zinc-600 px-4 py-2 text-sm text-zinc-400 hover:text-white">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">Update Product</button>
            </div>
        </form>
    </div>
</x-app-layout>
