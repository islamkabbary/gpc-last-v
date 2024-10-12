<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VisitResource extends JsonResource
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
            'unit_id' => $this->unit_id,
            'unit_type_id' => $this->unit_type_id,
            'team_id' => $this->team_id,
            'date' => $this->date,
            'time' => $this->time,
            'service_id' => $this->service_id,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
            'tools' => ToolResource::collection($this->whenLoaded('tools')),
        ];
    }
}
