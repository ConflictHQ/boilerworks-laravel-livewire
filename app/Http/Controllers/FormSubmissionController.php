<?php

namespace App\Http\Controllers;

use App\Enums\FormStatus;
use App\Models\FormDefinition;
use App\Models\FormSubmission;
use App\Rules\ValidFormSubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FormSubmissionController extends Controller
{
    public function index(FormDefinition $form): View
    {
        return view('forms.submissions.index', [
            'form' => $form,
            'submissions' => $form->submissions()
                ->orderBy('created_at', 'desc')
                ->paginate(25),
        ]);
    }

    public function create(FormDefinition $form): View
    {
        abort_unless($form->status === FormStatus::Published, 404);

        return view('forms.submissions.create', [
            'form' => $form,
        ]);
    }

    public function store(Request $request, FormDefinition $form): RedirectResponse
    {
        abort_unless($form->status === FormStatus::Published, 404);

        $validated = $request->validate([
            'data' => ['required', 'array', new ValidFormSubmission($form)],
        ]);

        FormSubmission::create([
            'form_definition_id' => $form->id,
            'data' => $validated['data'],
        ]);

        return redirect()
            ->route('forms.show', $form)
            ->with('success', 'Form submitted successfully.');
    }
}
