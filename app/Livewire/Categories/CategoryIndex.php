<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.app-layout')]
class CategoryIndex extends Component
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
        $category = Category::where('uuid', $uuid)->firstOrFail();
        $category->delete();

        session()->flash('success', 'Category deleted.');
    }

    public function render(): View
    {
        $query = Category::withCount('items')
            ->orderBy('created_at', 'desc');

        if ($this->search !== '') {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        return view('livewire.categories.category-index', [
            'categories' => $query->paginate(25),
        ]);
    }
}
