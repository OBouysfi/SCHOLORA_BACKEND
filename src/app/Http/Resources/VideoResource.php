<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tutor_id' => $this->id, 
            'intro_video' => $this->intro_video ? asset('storage/'.$this->intro_video) : null,
            'video_link' => $this->video_link,
            'thumbnail' => $this->video_thumbnail ? asset('storage/'.$this->video_thumbnail) : null,
        ];
    }
}
