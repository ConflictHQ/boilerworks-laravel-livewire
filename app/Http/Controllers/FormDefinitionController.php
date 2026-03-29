<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormDefinitionRequest;
use App\Http\Requests\UpdateFormDefinitionRequest;
use App\Models\FormDefinition;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FormDefinitionController extends Controller
{
    public function index(): View
    {
        return view('forms.index', [
            'forms' => FormDefinition::withCount('submissions')
                ->orderBy('created_at', 'desc')
                ->paginate(25),
        ]);
    }

    public function create(): View
    {
        return view('forms.create');
    }

    public function store(StoreFormDefinitionRequest $request): RedirectResponse
    {
        FormDefinition::create($request->validated());

        return redirect()
            ->route('forms.index')
            ->with('success', 'Form created.');
    }

    public function show(FormDefinition $form): View
    {
        return view('forms.show', [
            'form' => $form->loadCount('submissions')->load('creator'),
        ]);
    }

    public function edit(FormDefinition $form): View
    {
        return view('forms.edit', [
            'form' => $form,
        ]);
    }

    public function update(UpdateFormDefinitionRequest $request, FormDefinition $form): RedirectResponse
    {
        $form->update($request->validated());

        return redirect()
            ->route('forms.show', $form)
            ->with('success', 'Form updated.');
    }

    public function destroy(FormDefinition $form): RedirectResponse
    {
        $form->delete();

        return redirect()
            ->route('forms.index')
            ->with('success', 'Form deleted.');
    }
}
