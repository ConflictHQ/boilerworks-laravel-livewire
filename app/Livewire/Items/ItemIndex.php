<?php

namespace App\Livewire\Items;

use App\Models\Item;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.app-layout')]
class ItemIndex extends Component
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
        $item = Item::where('uuid', $uuid)->firstOrFail();
        $item->delete();

        session()->flash('success', 'Item deleted.');
    }

    public function render(): View
    {
        $query = Item::with('category')
            ->orderBy('created_at', 'desc');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        return view('livewire.items.item-index', [
            'items' => $query->paginate(25),
        ]);
    }
}
