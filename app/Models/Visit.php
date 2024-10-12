<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(VisitMaterial::class, 'visit_id', 'id');
    }

    public function tools()
    {
        return $this->belongsToMany(Tool::class, 'visit_tools')->withPivot('quantity', 'cost');
    }


    public function sites(): HasMany
    {
        return $this->hasMany(VisitSite::class, 'visit_id', 'id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(VisitImage::class, 'visit_id', 'id');
    }
}
