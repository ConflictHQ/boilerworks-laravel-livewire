<?php

namespace App\Services;

use App\Models\WorkflowInstance;
use Illuminate\Support\Facades\Auth;

class ConditionEvaluator
{
    /**
     * Evaluate a list of conditions against the current context.
     *
     * Each condition is an associative array with at least a 'type' key.
     * Returns true only if every condition passes.
     *
     * @param  array<int, array{type: string, ...}>  $conditions
     */
    public static function evaluate(array $conditions, WorkflowInstance $instance): bool
    {
        foreach ($conditions as $condition) {
            if (! self::evaluateCondition($condition, $instance)) {
                return false;
            }
        }

        return true;
    }

    private static function evaluateCondition(array $condition, WorkflowInstance $instance): bool
    {
        return match ($condition['type'] ?? null) {
            'user_has_role' => self::userHasRole($condition),
            'field_equals' => self::fieldEquals($condition, $instance),
            'field_in' => self::fieldIn($condition, $instance),
            'is_authenticated' => Auth::check(),
            default => false,
        };
    }

    private static function userHasRole(array $condition): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return $user->hasRole($condition['role'] ?? '');
    }

    private static function fieldEquals(array $condition, WorkflowInstance $instance): bool
    {
        $workflowable = $instance->workflowable;

        if (! $workflowable) {
            return false;
        }

        $field = $condition['field'] ?? '';
        $value = $condition['value'] ?? null;

        return data_get($workflowable, $field) === $value;
    }

    private static function fieldIn(array $condition, WorkflowInstance $instance): bool
    {
        $workflowable = $instance->workflowable;

        if (! $workflowable) {
            return false;
        }

        $field = $condition['field'] ?? '';
        $values = $condition['values'] ?? [];

        return in_array(data_get($workflowable, $field), $values, true);
    }
}
