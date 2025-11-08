<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StudentRegistrationRequest;
use App\Http\Resources\Student\StudentResource;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class StudentRegistrationController extends Controller
{
    public function store(StudentRegistrationRequest $request)
    {
        $student = Student::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return response()->json([
            'message' => 'Student registered successfully.',
            'student' => $student,
        ], 201);
    }

}
