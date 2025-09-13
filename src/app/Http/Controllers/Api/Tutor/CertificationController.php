<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\CertificationRequest;
use App\Http\Resources\CertificationResource;
use App\Models\TutorCertification;
use Illuminate\Support\Facades\Storage;

class CertificationController extends Controller
{
    /**
     * Store tutor certifications
     */
    public function store_certifications(CertificationRequest $request)
    {
        $tutorId = $request->input('tutor_id');
        $certificationsData = $request->input('certifications');

        $storedCertifications = [];

        foreach ($certificationsData as $index => $cert) {
            $filePath = null;

            // Correct way to get uploaded file
            if ($request->hasFile("certifications.$index.file")) {
                $file = $request->file("certifications.$index.file");
                $filePath = $file->store('tutor_certifications', 'public');
            }

            $tutorCertification = TutorCertification::create([
                'tutor_id' => $tutorId,
                'subject' => $cert['subject'] ?? null,
                'certification_name' => $cert['certification'] ?? null,
                'is_custom_certification' => false,
                'custom_certification_name' => null,
                'year_from' => $cert['years_from'] ?? null,
                'year_to' => $cert['years_to'] ?? null,
                'certificate_file' => $filePath,
                'verification_status' => 'pending',
            ]);

            $storedCertifications[] = $tutorCertification;
        }

        return response()->json([
            'success' => true,
            'message' => 'Tutor certifications saved successfully',
            'data' => CertificationResource::collection($storedCertifications)
        ]);
    }
}
