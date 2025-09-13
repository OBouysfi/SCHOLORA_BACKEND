<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutStepResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'email'      => $this->email,
            'country'    => $this->country,
            'phone'      => $this->phone,
            'is_over_18' => $this->is_over_18,
            'subjects'   => $this->subjects->pluck('subject'),
            'languages'  => $this->languages->map(fn($lang) => [
                'language' => $lang->language,
                'level'    => $lang->level,
            ]),
            'status'     => $this->status,
        ];
    }
}
