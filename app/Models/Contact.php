<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    public function contactType()
    {
        return $this->belongsTo(ContactType::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
