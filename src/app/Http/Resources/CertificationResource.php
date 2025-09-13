<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CertificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'tutor_id' => $this->tutor_id,
            'subject' => $this->subject,
            'certification_name' => $this->certification_name,
            'is_custom_certification' => $this->is_custom_certification,
            'custom_certification_name' => $this->custom_certification_name,
            'year_from' => $this->year_from,
            'year_to' => $this->year_to,
            'certificate_file_url' => $this->certificate_file ? asset('storage/' . $this->certificate_file) : null,
            'verification_status' => $this->verification_status,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
