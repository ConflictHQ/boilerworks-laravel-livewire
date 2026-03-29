<?php

namespace App\Models;

use App\Traits\HasAuditTrail;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class WorkflowInstance extends Model
{
    use HasAuditTrail;
    use HasFactory;
    use HasUuid;

    protected $fillable = ['workflow_definition_id', 'workflowable_type', 'workflowable_id', 'current_state'];

    protected $hidden = ['id'];

    public function definition(): BelongsTo
    {
        return $this->belongsTo(WorkflowDefinition::class, 'workflow_definition_id');
    }

    public function workflowable(): MorphTo
    {
        return $this->morphTo();
    }

    public function transitionLogs(): HasMany
    {
        return $this->hasMany(TransitionLog::class);
    }
}
