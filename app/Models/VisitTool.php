<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VisitTool extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class, 'tool_id', 'id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(VisitToolUser::class, 'visit_tool_id', 'id');
    }
}
