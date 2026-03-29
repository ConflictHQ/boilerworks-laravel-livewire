<?php

namespace Database\Factories;

use App\Enums\WorkflowStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowDefinitionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'status' => fake()->randomElement(WorkflowStatus::cases()),
            'states' => [
                [
                    'name' => 'draft',
                    'label' => 'Draft',
                    'is_initial' => true,
                    'is_final' => false,
                    'color' => 'gray',
                ],
                [
                    'name' => 'pending_review',
                    'label' => 'Pending Review',
                    'is_initial' => false,
                    'is_final' => false,
                    'color' => 'yellow',
                ],
                [
                    'name' => 'approved',
                    'label' => 'Approved',
                    'is_initial' => false,
                    'is_final' => true,
                    'color' => 'green',
                ],
                [
                    'name' => 'rejected',
                    'label' => 'Rejected',
                    'is_initial' => false,
                    'is_final' => true,
                    'color' => 'red',
                ],
            ],
            'transitions' => [
                [
                    'from' => 'draft',
                    'to' => 'pending_review',
                    'label' => 'Submit for Review',
                    'conditions' => [],
                    'actions' => [],
                ],
                [
                    'from' => 'pending_review',
                    'to' => 'approved',
                    'label' => 'Approve',
                    'conditions' => [],
                    'actions' => [],
                ],
                [
                    'from' => 'pending_review',
                    'to' => 'rejected',
                    'label' => 'Reject',
                    'conditions' => [],
                    'actions' => [],
                ],
                [
                    'from' => 'rejected',
                    'to' => 'draft',
                    'label' => 'Revise',
                    'conditions' => [],
                    'actions' => [],
                ],
            ],
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => WorkflowStatus::Published]);
    }
}
