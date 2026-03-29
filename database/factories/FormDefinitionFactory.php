<?php

namespace Database\Factories;

use App\Enums\FormStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormDefinitionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(3),
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(FormStatus::cases()),
            'schema' => [
                'fields' => [
                    [
                        'name' => 'full_name',
                        'label' => 'Full Name',
                        'type' => 'text',
                        'required' => true,
                        'placeholder' => 'Enter your name',
                        'options' => [],
                    ],
                    [
                        'name' => 'email',
                        'label' => 'Email',
                        'type' => 'email',
                        'required' => true,
                        'placeholder' => 'you@example.com',
                        'options' => [],
                    ],
                    [
                        'name' => 'message',
                        'label' => 'Message',
                        'type' => 'textarea',
                        'required' => false,
                        'placeholder' => '',
                        'options' => [],
                    ],
                ],
            ],
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => FormStatus::Published]);
    }
}
