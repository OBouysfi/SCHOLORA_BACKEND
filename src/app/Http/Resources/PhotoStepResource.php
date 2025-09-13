<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhotoStepResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'first_name'    => $this->first_name,
            'last_name'     => $this->last_name,
            'profile_photo' => $this->profile_photo 
                ? asset('storage/' . $this->profile_photo) 
                : null,
            'updated_at'    => $this->updated_at,
        ];
    }
}
