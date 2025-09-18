<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EducationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'tutor_id' => ['required', 'exists:tutors,id'],
            'education' => ['required', 'array'],
            'education.*.university' => ['required', 'string'],
            'education.*.degree' => ['required', 'string'],
            'education.*.degree_type' => ['nullable', 'string'],
            'education.*.specialization' => ['nullable', 'string'],
            'education.*.yearsFrom' => ['nullable', 'digits:4'],
            'education.*.yearsTo' => ['nullable', 'digits:4'],
            'education.*.diplomaFile' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:20480'],
        ];
    }

    /**
     * Custom error messages for validation
     */
    public function messages(): array
    {
        return [
            'tutor_id.required' => 'Tutor ID is required.',
            'tutor_id.exists' => 'The selected tutor does not exist.',
            'education.required' => 'Education information is required.',
            'education.array' => 'Education must be an array.',
            'education.*.university.required' => 'University name is required for each entry.',
            'education.*.degree.required' => 'Degree is required for each entry.',
            'education.*.degree_type.string' => 'Degree type must be a string.',
            'education.*.specialization.string' => 'Specialization must be a string.',
            'education.*.yearsFrom.digits' => 'Start year must be 4 digits.',
            'education.*.yearsTo.digits' => 'End year must be 4 digits.',
            'education.*.diplomaFile.file' => 'Diploma must be a valid file.',
            'education.*.diplomaFile.mimes' => 'Diploma must be a file of type: jpg, jpeg, png, pdf.',
            'education.*.diplomaFile.max' => 'Diploma file must not exceed 20MB.',
        ];
    }
}
