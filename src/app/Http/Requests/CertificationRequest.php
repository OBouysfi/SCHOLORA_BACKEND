<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tutor_id' => ['required', 'exists:tutors,id'],
            'certifications' => ['required', 'array'],
            'certifications.*.subject' => ['required', 'string'],
            'certifications.*.certification' => ['required', 'string'],
            'certifications.*.years_from' => ['required', 'string'],
            'certifications.*.years_to' => ['required', 'string'],
            'certifications.*.file' => ['required', 'file', 'mimes:jpg,png,pdf', 'max:20480'],
        ];
    }

    public function messages(): array
    {
        return [
            'tutor_id.required' => 'Tutor ID is required.',
            'tutor_id.exists' => 'The selected tutor does not exist.',
            'certifications.required' => 'You must provide at least one certification.',
            'certifications.array' => 'Certifications must be an array.',
            'certifications.*.subject.required' => 'Subject is required for each certification.',
            'certifications.*.certification.required' => 'Certification name is required.',
            'certifications.*.years_from.required' => 'Starting year is required.',
            'certifications.*.years_to.required' => 'Ending year is required.',
            'certifications.*.file.required' => 'Certificate file is required.',
            'certifications.*.file.mimes' => 'File must be JPG, PNG, or PDF.',
            'certifications.*.file.max' => 'File size cannot exceed 20MB.',
        ];
    }
}
