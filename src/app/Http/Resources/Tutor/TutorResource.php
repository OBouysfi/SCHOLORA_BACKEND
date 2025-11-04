<?php

namespace App\Http\Resources\Tutor;

use Illuminate\Http\Resources\Json\JsonResource;

class TutorResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'country' => $this->country,
            'subject' => $this->main_subject,
            'phone' => $this->phone,
            'isOver18' => $this->is_over_18,
            'profilePhoto' => $this->profile_photo,
            'description' => $this->description,
            'introVideo' => $this->intro_video,
            'hourlyRate' => $this->hourly_rate,
            'status' => $this->status,
            'createdAt' => $this->created_at
        ];
    }
}