<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tutor_id'    => ['required', 'exists:tutors,id'],
            'description' => ['required', 'string', 'min:50', 'max:400'],
        ];
    }

    public function messages(): array
    {
        return [
            'tutor_id.required'    => 'Tutor ID is required.',
            'tutor_id.exists'      => 'The selected tutor does not exist.',
            'description.required' => 'The description field is required.',
            'description.string'   => 'The description must be a string.',
            'description.min'      => 'The description must be at least 50 characters.',
            'description.max'      => 'The description may not exceed 400 characters.',
        ];
    }
}
