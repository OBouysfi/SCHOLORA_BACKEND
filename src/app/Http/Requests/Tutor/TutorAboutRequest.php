<?php

namespace App\Http\Requests\Tutor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TutorAboutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tutorId = $this->route('id');

        return [
            'firstName' => 'required|string|min:2|max:50',
            'lastName' => 'required|string|min:2|max:50',
            'email' => [
                'required',
                'email',
                Rule::unique('tutors', 'email')->ignore($tutorId)
            ],
            'country' => 'required|string',
            'subject' => 'required|string',
            'phone' => 'nullable|string',
            'isOver18' => 'required|accepted'
        ];
    }

    public function messages(): array
    {
        return [
            'firstName.required' => 'First name is required',
            'lastName.required' => 'Last name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email format',
            'email.unique' => 'Email already registered',
            'isOver18.accepted' => 'You must be 18 years or older'
        ];
    }
}