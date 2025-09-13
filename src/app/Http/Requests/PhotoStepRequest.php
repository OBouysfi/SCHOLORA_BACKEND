<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhotoStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tutor_id' => ['required', 'exists:tutors,id'], 
            'photo'    => ['required', 'image', 'max:2048'], 
        ];
    }

    public function messages(): array
    {
        return [
            'tutor_id.required' => 'Tutor ID is required',
            'tutor_id.exists'   => 'Tutor not found',
            'photo.required'    => 'Profile photo is required',
            'photo.image'       => 'The file must be an image',
            'photo.max'         => 'The image must not exceed 2MB',
        ];
    }
}
