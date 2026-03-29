<?php

namespace App\Services;

use App\Jobs\WorkflowActionJob;
use App\Models\TransitionLog;
use App\Models\WorkflowInstance;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkflowTransitionService
{
    /**
     * Transition a workflow instance to a new state.
     *
     * @throws ValidationException
     */
    public function transition(WorkflowInstance $instance, string $toState): WorkflowInstance
    {
        $definition = $instance->definition;
        $fromState = $instance->current_state;

        $transition = $this->findTransition($definition->transitions, $fromState, $toState);

        if (! $transition) {
            throw ValidationException::withMessages([
                'to_state' => ["No transition exists from '{$fromState}' to '{$toState}'."],
            ]);
        }

        $conditions = $transition['conditions'] ?? [];

        if (! ConditionEvaluator::evaluate($conditions, $instance)) {
            throw ValidationException::withMessages([
                'to_state' => ['Transition conditions are not met.'],
            ]);
        }

        return DB::transaction(function () use ($instance, $fromState, $toState, $transition) {
            $instance->update(['current_state' => $toState]);

            TransitionLog::create([
                'workflow_instance_id' => $instance->id,
                'from_state' => $fromState,
                'to_state' => $toState,
                'performed_by' => auth()->id(),
            ]);

            foreach ($transition['actions'] ?? [] as $action) {
                WorkflowActionJob::dispatch($instance, $action);
            }

            return $instance->fresh();
        });
    }

    /**
     * @param  array<int, array{from: string, to: string, ...}>  $transitions
     */
    private function findTransition(array $transitions, string $from, string $to): ?array
    {
        foreach ($transitions as $transition) {
            if (($transition['from'] ?? '') === $from && ($transition['to'] ?? '') === $to) {
                return $transition;
            }
        }

        return null;
    }
}
