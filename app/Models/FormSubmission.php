<?php

namespace App\Models;

use App\Traits\HasAuditTrail;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSubmission extends Model
{
    use HasAuditTrail;
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $fillable = ['form_definition_id', 'data'];

    protected $hidden = ['id'];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function definition(): BelongsTo
    {
        return $this->belongsTo(FormDefinition::class, 'form_definition_id');
    }
}
