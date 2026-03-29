<?php

namespace App\Rules;

use App\Models\FormDefinition;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidFormSubmission implements ValidationRule
{
    public function __construct(
        private readonly FormDefinition $definition
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail('The submission data must be an object.');

            return;
        }

        $fields = $this->definition->schema['fields'] ?? [];

        foreach ($fields as $field) {
            $fieldName = $field['name'] ?? '';
            $fieldValue = $value[$fieldName] ?? null;
            $label = $field['label'] ?? $fieldName;
            $required = $field['required'] ?? false;
            $type = $field['type'] ?? 'text';

            if ($required && blank($fieldValue)) {
                $fail("{$label} is required.");

                continue;
            }

            if (blank($fieldValue)) {
                continue;
            }

            match ($type) {
                'email' => filter_var($fieldValue, FILTER_VALIDATE_EMAIL) || $fail("{$label} must be a valid email address."),
                'number' => is_numeric($fieldValue) || $fail("{$label} must be a number."),
                'date' => strtotime($fieldValue) !== false || $fail("{$label} must be a valid date."),
                'select', 'radio' => in_array($fieldValue, array_column($field['options'] ?? [], 'value'), true) || $fail("{$label} contains an invalid selection."),
                default => null,
            };
        }
    }
}
