<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'tutor_id' => 'required|exists:tutors,id',
            'intro_video' => 'required|file|mimetypes:video/mp4,video/avi,video/mov|max:20000', 
            'video_link' => 'nullable|url',
            'thumbnail' => 'nullable|image|max:5000',
        ];
    }
}
