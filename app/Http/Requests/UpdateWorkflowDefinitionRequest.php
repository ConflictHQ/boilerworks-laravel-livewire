<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkflowDefinitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:draft,published,archived'],
            'states' => ['required', 'array', 'min:1'],
            'states.*.name' => ['required', 'string', 'max:255'],
            'states.*.label' => ['required', 'string', 'max:255'],
            'states.*.is_initial' => ['required', 'boolean'],
            'states.*.is_final' => ['required', 'boolean'],
            'states.*.color' => ['nullable', 'string', 'max:50'],
            'transitions' => ['present', 'array'],
            'transitions.*.from' => ['required', 'string', 'max:255'],
            'transitions.*.to' => ['required', 'string', 'max:255'],
            'transitions.*.label' => ['required', 'string', 'max:255'],
            'transitions.*.conditions' => ['present', 'array'],
            'transitions.*.actions' => ['present', 'array'],
        ];
    }
}
