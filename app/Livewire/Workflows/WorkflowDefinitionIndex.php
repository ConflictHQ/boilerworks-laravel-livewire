<?php

namespace App\Livewire\Workflows;

use App\Models\WorkflowDefinition;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.app-layout')]
class WorkflowDefinitionIndex extends Component
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
        $workflow = WorkflowDefinition::where('uuid', $uuid)->firstOrFail();
        $workflow->delete();

        session()->flash('success', 'Workflow deleted.');
    }

    public function render(): View
    {
        $query = WorkflowDefinition::withCount('instances')
            ->orderBy('created_at', 'desc');

        if ($this->search !== '') {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        return view('livewire.workflows.workflow-definition-index', [
            'workflows' => $query->paginate(25),
        ]);
    }
}
