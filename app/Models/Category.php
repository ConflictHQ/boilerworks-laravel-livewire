<?php

namespace App\Models;

use App\Traits\HasAuditTrail;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasAuditTrail;
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $fillable = ['name', 'description'];

    protected $hidden = ['id'];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
