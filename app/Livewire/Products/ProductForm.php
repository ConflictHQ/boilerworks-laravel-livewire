<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class ProductForm extends Component
{
    public ?Product $product = null;

    public string $name = '';

    public string $description = '';

    public string $price = '';

    public string $status = 'draft';

    public string $category_id = '';

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->product = $product;
            $this->name = $product->name;
            $this->description = $product->description ?? '';
            $this->price = (string) $product->price;
            $this->status = $product->status;
            $this->category_id = (string) ($product->category_id ?? '');
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'string', 'in:active,draft,archived'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);

        if ($validated['category_id'] === '') {
            $validated['category_id'] = null;
        }

        if ($this->product) {
            $this->product->update($validated);
            session()->flash('success', 'Product updated.');

            $this->redirect(route('livewire.products.index'), navigate: true);
        } else {
            Product::create($validated);
            session()->flash('success', 'Product created.');

            $this->redirect(route('livewire.products.index'), navigate: true);
        }
    }

    public function render(): View
    {
        return view('livewire.products.product-form', [
            'categories' => Category::orderBy('name')->get(['id', 'uuid', 'name']),
        ]);
    }
}
