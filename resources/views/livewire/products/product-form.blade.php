<div>
    <h1 class="text-2xl font-bold mb-6">{{ $product ? 'Edit Product' : 'Create Product' }}</h1>
    <div class="bg-zinc-800 rounded-xl border border-zinc-700 p-6">
        <form wire:submit="save" class="space-y-4">
            <div>
                <label for="name" class="block text-sm text-zinc-400 mb-1">Name</label>
                <input type="text" wire:model="name" id="name" class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">
                @error('name') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="description" class="block text-sm text-zinc-400 mb-1">Description</label>
                <textarea wire:model="description" id="description" rows="3" class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100"></textarea>
                @error('description') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="price" class="block text-sm text-zinc-400 mb-1">Price</label>
                    <input type="number" step="0.01" wire:model="price" id="price" class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">
                    @error('price') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm text-zinc-400 mb-1">Status</label>
                    <select wire:model="status" id="status" class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">
                        <option value="active">Active</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                    @error('status') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label for="category_id" class="block text-sm text-zinc-400 mb-1">Category</label>
                <select wire:model="category_id" id="category_id" class="w-full rounded-lg bg-zinc-900 border border-zinc-700 px-4 py-2 text-zinc-100">
                    <option value="">-- None --</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end space-x-3">
                <a href="{{ route('livewire.products.index') }}" class="rounded-lg border border-zinc-600 px-4 py-2 text-sm text-zinc-400 hover:text-white" wire:navigate>Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">
                    {{ $product ? 'Update Product' : 'Create Product' }}
                </button>
            </div>
        </form>
    </div>
</div>
