<?php

namespace App\Http\Controllers\api;

use App\Traits\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Client;
use App\Models\Unit;

class UnitController extends Controller
{
    use ResponseTrait;

    public function show($client_id)
    {
        $client = Client::find($client_id);
        if (!$client) {
            return $this->error('Client not found', 404);
        }
        $unit = Unit::where('client_id', $client_id)->first();

        if (!$unit) {
            return $this->error('Unit not found', 404);
        }

        return $this->success(
            new UnitResource($unit),
            'Unit retrieved successfully'
        );
    }
}
