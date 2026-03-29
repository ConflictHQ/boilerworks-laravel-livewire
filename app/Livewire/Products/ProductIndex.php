<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.app-layout')]
class ProductIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function delete(string $uuid): void
    {
        $product = Product::where('uuid', $uuid)->firstOrFail();
        $product->delete();

        session()->flash('success', 'Product deleted.');
    }

    public function render(): View
    {
        $query = Product::with('category')
            ->orderBy('created_at', 'desc');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        return view('livewire.products.product-index', [
            'products' => $query->paginate(25),
        ]);
    }
}
