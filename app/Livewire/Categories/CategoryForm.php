<?php

namespace App\Livewire\Categories;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.app-layout')]
class CategoryForm extends Component
{
    public ?Category $category = null;

    public string $name = '';

    public string $description = '';

    public function mount(?Category $category = null): void
    {
        if ($category?->exists) {
            $this->category = $category;
            $this->name = $category->name;
            $this->description = $category->description ?? '';
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($this->category) {
            $this->category->update($validated);
            session()->flash('success', 'Category updated.');
        } else {
            Category::create($validated);
            session()->flash('success', 'Category created.');
        }

        $this->redirect(route('livewire.categories.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.categories.category-form');
    }
}
