<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function values(): HasMany
    {
        return $this->hasMany(ServiceValue::class, 'service_id', 'id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(ServiceNote::class, 'service_id', 'id');
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id', 'id');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'service_id', 'id');
    }

    public function quotations()
    {
        return $this->hasMany(Quotation::class, 'service_id', 'id');
    }
}
