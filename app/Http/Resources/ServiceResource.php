<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'client_id' => $this->client_id,
            'type_of_injury' => $this->type_of_injury,
            'level_injury' => $this->level_injury,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'visits' => VisitResource::collection($this->whenLoaded('visits')),
            'contracts' => $this->contracts,
            'quotations' => $this->quotations,
        ];
    }
}
