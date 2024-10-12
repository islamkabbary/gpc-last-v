<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BaseModel extends Model
{
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if (in_array($key, ['created_at', 'updated_at'])) {
            return $value ? Carbon::parse($value)->format('d-m-Y') : null;
        }

        return $value;
    }
}
