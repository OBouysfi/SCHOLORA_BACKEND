<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AboutStepRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Must be true to allow validation
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email:rfc,dns',
                'unique:tutors,email',
                function ($attribute, $value, $fail) {
                    $allowedDomains = ['gmail.com', 'yahoo.com', 'outlook.com']; 
                    $emailDomain = substr(strrchr($value, "@"), 1);

                    if (!in_array($emailDomain, $allowedDomains)) {
                        $fail("Email must be from one of the following domains: " . implode(', ', $allowedDomains));
                    }
                }
            ],
            'country'    => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'max:20'],
            'isOver18'   => ['required', 'boolean'],
            'subject'    => ['required', 'string'],
            'languages'  => ['required', 'array', 'min:1'],
            'languages.*.language' => ['required', 'string'],
            'languages.*.level'    => ['required', 'string', 'in:A1,A2,B1,B2,C1,C2,Native'],
        ];
    }

    /**
     * Custom error messages for validation
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required'  => 'Last name is required',
            'email.required'      => 'Email address is required',
            'email.email'         => 'Email address is invalid',
            'email.unique'        => 'This email is already taken',
            'country.required'    => 'Country is required',
            'phone.required'      => 'Phone number is required',
            'isOver18.required'   => 'You must confirm that you are over 18',
            'subject.required'    => 'Subject is required',
            'languages.required'  => 'At least one language is required',
            'languages.*.language.required' => 'Language name is required',
            'languages.*.level.required'    => 'Language level is required',
            'languages.*.level.in'          => 'Level must be one of: A1,A2,B1,B2,C1,C2,Native',
        ];
    }

    /**
     * Prepare the data for validation
     */
    public function prepareForValidation(): void
    {
        $this->merge([
            'is_over_18' => $this->isOver18,
        ]);
    }
}
