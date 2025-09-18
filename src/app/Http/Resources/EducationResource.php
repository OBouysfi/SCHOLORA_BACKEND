<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EducationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'tutor_id' => $this->tutor_id,
            'university' => $this->university,
            'degree' => $this->degree,
            'degree_type' => $this->degree_type,
            'specialization' => $this->specialization,
            'year_from' => $this->year_from,
            'year_to' => $this->year_to,
            'diploma_file' => $this->diploma_file ? asset('storage/' . $this->diploma_file) : null,
            'verification_status' => $this->verification_status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
