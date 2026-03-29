<?php

namespace App\Models;

use App\Enums\FormStatus;
use App\Traits\HasAuditTrail;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormDefinition extends Model
{
    use HasAuditTrail;
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $fillable = ['name', 'slug', 'description', 'status', 'schema'];

    protected $hidden = ['id'];

    protected function casts(): array
    {
        return [
            'schema' => 'array',
            'status' => FormStatus::class,
        ];
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class);
    }
}
