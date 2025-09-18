<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DescriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id'          => $this->id,
            'tutor_id'    => $this->id, 
            'description' => $this->description,
        ];
    }
}
