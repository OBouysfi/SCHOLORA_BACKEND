<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\EducationRequest;
use App\Http\Resources\EducationResource;
use App\Models\TutorEducation;
use Illuminate\Support\Facades\Storage;

class EducationController extends Controller
{
    /**
     * Store multiple education entries for a tutor.
     */
    public function store_Education(EducationRequest $request)
    {
        $tutorId = $request->tutor_id;
        $educationsData = $request->education;

        $createdEducations = [];

        foreach ($educationsData as $edu) {
            $diplomaPath = null;

            if (isset($edu['diplomaFile']) && $edu['diplomaFile']) {
                $diplomaPath = $edu['diplomaFile']->store('diplomas', 'public');
            }

            $createdEducations[] = TutorEducation::create([
                'tutor_id' => $tutorId,
                'university' => $edu['university'],
                'degree' => $edu['degree'],
                'degree_type' => $edu['degree_type'] ?? null,
                'specialization' => $edu['specialization'] ?? null,
                'year_from' => $edu['yearsFrom'] ?? null,
                'year_to' => $edu['yearsTo'] ?? null,
                'diploma_file' => $diplomaPath,
            ]);
        }

        return EducationResource::collection($createdEducations);
    }
}
