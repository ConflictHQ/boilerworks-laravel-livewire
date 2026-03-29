<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkflowDefinitionRequest;
use App\Http\Requests\UpdateWorkflowDefinitionRequest;
use App\Models\WorkflowDefinition;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WorkflowDefinitionController extends Controller
{
    public function index(): View
    {
        return view('workflows.index', [
            'workflows' => WorkflowDefinition::withCount('instances')
                ->orderBy('created_at', 'desc')
                ->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('workflows.create');
    }

    public function store(StoreWorkflowDefinitionRequest $request): RedirectResponse
    {
        WorkflowDefinition::create($request->validated());

        return redirect()
            ->route('workflows.index')
            ->with('success', 'Workflow created.');
    }

    public function show(WorkflowDefinition $workflow): View
    {
        return view('workflows.show', [
            'workflow' => $workflow->loadCount('instances')->load('creator'),
        ]);
    }

    public function edit(WorkflowDefinition $workflow): View
    {
        return view('workflows.edit', [
            'workflow' => $workflow,
        ]);
    }

    public function update(UpdateWorkflowDefinitionRequest $request, WorkflowDefinition $workflow): RedirectResponse
    {
        $workflow->update($request->validated());

        return redirect()
            ->route('workflows.show', $workflow)
            ->with('success', 'Workflow updated.');
    }

    public function destroy(WorkflowDefinition $workflow): RedirectResponse
    {
        $workflow->delete();

        return redirect()
            ->route('workflows.index')
            ->with('success', 'Workflow deleted.');
    }
}
