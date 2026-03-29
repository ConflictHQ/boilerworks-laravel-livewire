<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFormDefinitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:form_definitions,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:draft,published,archived'],
            'schema' => ['required', 'array'],
            'schema.fields' => ['present', 'array'],
            'schema.fields.*.name' => ['required', 'string'],
            'schema.fields.*.label' => ['required', 'string'],
            'schema.fields.*.type' => ['required', 'string', 'in:text,textarea,number,email,select,checkbox,radio,date'],
        ];
    }
}
