<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'client_id' => $this->client_id,
            'status' => $this->status,
            'description' => $this->description,
            'number_of_services' => $this->number_of_services,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'contacts' => ContactResource::collection($this->contacts),
            'units' => UnitResource::collection($this->units),
        ];
    }
}

