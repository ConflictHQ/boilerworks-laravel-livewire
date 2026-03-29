<?php

namespace App\Models;

use App\Enums\WorkflowStatus;
use App\Traits\HasAuditTrail;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkflowDefinition extends Model
{
    use HasAuditTrail;
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'status', 'states', 'transitions'];

    protected $hidden = ['id'];

    protected function casts(): array
    {
        return [
            'states' => 'array',
            'transitions' => 'array',
            'status' => WorkflowStatus::class,
        ];
    }

    public function instances(): HasMany
    {
        return $this->hasMany(WorkflowInstance::class);
    }
}
