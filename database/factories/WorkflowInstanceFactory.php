<?php

namespace Database\Factories;

use App\Models\WorkflowDefinition;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkflowInstanceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'workflow_definition_id' => WorkflowDefinition::factory(),
            'workflowable_type' => null,
            'workflowable_id' => null,
            'current_state' => 'draft',
        ];
    }
}
