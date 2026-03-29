<?php

namespace App\Livewire\Forms;

use App\Models\FormDefinition;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.app-layout')]
class FormDefinitionIndex extends Component
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
        $form = FormDefinition::where('uuid', $uuid)->firstOrFail();
        $form->delete();

        session()->flash('success', 'Form deleted.');
    }

    public function render(): View
    {
        $query = FormDefinition::withCount('submissions')
            ->orderBy('created_at', 'desc');

        if ($this->search !== '') {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        return view('livewire.forms.form-definition-index', [
            'forms' => $query->paginate(25),
        ]);
    }
}
