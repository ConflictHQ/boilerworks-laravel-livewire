<?php

namespace App\Livewire\Items;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class ItemForm extends Component
{
    public ?Item $item = null;

    public string $name = '';

    public string $description = '';

    public string $price = '';

    public string $status = 'draft';

    public string $category_id = '';

    public function mount(?Item $item = null): void
    {
        if ($item?->exists) {
            $this->item = $item;
            $this->name = $item->name;
            $this->description = $item->description ?? '';
            $this->price = (string) $item->price;
            $this->status = $item->status;
            $this->category_id = (string) ($item->category_id ?? '');
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

        if ($this->item) {
            $this->item->update($validated);
            session()->flash('success', 'Item updated.');

            $this->redirect(route('livewire.items.index'), navigate: true);
        } else {
            Item::create($validated);
            session()->flash('success', 'Item created.');

            $this->redirect(route('livewire.items.index'), navigate: true);
        }
    }

    public function render(): View
    {
        return view('livewire.items.item-form', [
            'categories' => Category::orderBy('name')->get(['id', 'uuid', 'name']),
        ]);
    }
}
