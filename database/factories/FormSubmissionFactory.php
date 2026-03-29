<?php

namespace Database\Factories;

use App\Models\FormDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormSubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'form_definition_id' => FormDefinition::factory(),
            'data' => [
                'full_name' => fake()->name(),
                'email' => fake()->email(),
                'message' => fake()->paragraph(),
            ],
        ];
    }
}
