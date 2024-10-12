<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $hidden = ['id'];

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class, 'client_id', 'id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'client_id', 'id');
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'client_id', 'id');
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sale_id', 'id');
    }
}
