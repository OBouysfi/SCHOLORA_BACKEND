<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tutor\TutorResource;
use App\Models\Tutor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\TutorCertification;
use App\Models\TutorEducation;
use App\Models\TutorAvailability;
use App\Models\User;


class TutorRegistrationController extends Controller
{

    public function saveAboutStep(Request $request): JsonResponse
    {
        try {
            Log::info('Tutor registration request', $request->all());

            $validated = $request->validate([
                'firstName' => 'required|string|min:2|max:50',
                'lastName'  => 'required|string|min:2|max:50',
                'email'     => 'required|email|unique:users,email',
                'country'   => 'required|string',
                'subject'   => 'required|string',
                'phone'     => 'nullable|string',
                'isOver18'  => 'required|boolean'
            ]);

            // Create locked user with random password
            $randomPassword = Str::random(40);

            $user = User::create([
                'first_name' => $request->firstName,
                'last_name'  => $request->lastName,
                'email'      => $request->email,
                'password'   => Hash::make($randomPassword),
                'is_active'  => false,
            ]);

            // Create tutor profile
            $tutor = Tutor::create([
                'user_id'      => $user->id,
                'first_name'   => $request->firstName,
                'last_name'    => $request->lastName,
                'email'        => $request->email,
                'country'      => $request->country,
                'main_subject' => $request->subject,
                'phone'        => $request->phone,
                'is_over_18'   => $request->isOver18,
                'status'       => 'draft'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tutor account created. Password setup required.',
                'data' => new TutorResource($tutor)
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Tutor registration failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getTutorDraft(string $email): JsonResponse
    {
        $tutor = Tutor::where('email', $email)->first();

        if (!$tutor) {
            return response()->json([
                'success' => false,
                'message' => 'No draft found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new TutorResource($tutor)
        ], 200);
    }

    public function savePhotoStep(Request $request): JsonResponse
    {
        try {

            $validated = $request->validate([
                'tutorId' => 'required|exists:tutors,id',
                'photo'   => 'required|image|mimes:jpeg,png,jpg|max:5120'
            ]);

            $tutor = Tutor::findOrFail($validated['tutorId']);

            if ($request->hasFile('photo')) {

                // Delete old photo if exists
                if ($tutor->profile_photo) {
                    Storage::disk('public')->delete($tutor->profile_photo);
                }

                // Store new photo
                $path = $request->file('photo')
                                ->store('tutors/photos', 'public');

                $tutor->update([
                    'profile_photo' => $path
                ]);
            }

            // Generate public URL
            $photoUrl = $tutor->profile_photo
                ? Storage::disk('public')->url($tutor->profile_photo)
                : null;

            return response()->json([
                'success' => true,
                'message' => 'Photo saved successfully',
                'data' => [
                    'tutor' => new TutorResource($tutor),
                    'profilePhotoUrl' => $photoUrl
                ]
            ], 200);

        } catch (\Exception $e) {

            Log::error('Error saving photo:', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving photo',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function saveCertificationStep(Request $request): JsonResponse
    {
        try {
            // Convertir hasNoCertificate en boolean
            $hasNoCertificate = $request->hasNoCertificate === 'true' || $request->hasNoCertificate === true;

            $validated = $request->validate([
                'tutorId' => 'required|exists:tutors,id',
                'certifications' => 'array',
                'certifications.*.subject' => 'nullable|string',
                'certifications.*.certification' => 'nullable|string',
                'certifications.*.yearsFrom' => 'nullable|integer',
                'certifications.*.yearsTo' => 'nullable|integer',
                'certifications.*.file' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:20480'
            ]);

            $tutor = Tutor::findOrFail($request->tutorId);

            // Supprimer les anciennes certifications
            $tutor->certifications()->delete();

            // Si pas de certificat, juste retourner
            if ($hasNoCertificate) {
                return response()->json([
                    'success' => true,
                    'message' => 'Certification step saved successfully',
                    'data' => new TutorResource($tutor)
                ], 200);
            }

            // Créer les nouvelles certifications
            if ($request->has('certifications')) {
                foreach ($request->certifications as $index => $certData) {
                    $filePath = null;
                    
                    if ($request->hasFile("certifications.$index.file")) {
                        $filePath = $request->file("certifications.$index.file")
                            ->store('tutors/certifications', 'public');
                    }

                    TutorCertification::create([
                        'tutor_id' => $tutor->id,
                        'subject' => $certData['subject'] ?? null,
                        'certification_name' => $certData['certification'] ?? null,
                        'year_from' => $certData['yearsFrom'] ?? null,
                        'year_to' => $certData['yearsTo'] ?? null,
                        'certificate_file' => $filePath,
                        'verification_status' => 'pending'
                    ]);
                }
            }

            Log::info('Certifications saved successfully:', ['id' => $tutor->id]);

            return response()->json([
                'success' => true,
                'message' => 'Certifications saved successfully',
                'data' => new TutorResource($tutor)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error saving certifications:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error saving certifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveEducationStep(Request $request): JsonResponse
    {
        try {
            $hasNoEducation = $request->hasNoEducation === 'true' || $request->hasNoEducation === true;

            $validated = $request->validate([
                'tutorId' => 'required|exists:tutors,id',
                'education' => 'array',
                'education.*.university' => 'nullable|string',
                'education.*.degree' => 'nullable|string',
                'education.*.degreeType' => 'nullable|string',
                'education.*.specialization' => 'nullable|string',
                'education.*.yearsFrom' => 'nullable|integer',
                'education.*.yearsTo' => 'nullable|integer',
                'education.*.diplomaFile' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:20480'
            ]);

            $tutor = Tutor::findOrFail($request->tutorId);
            $tutor->education()->delete();

            if ($hasNoEducation) {
                return response()->json([
                    'success' => true,
                    'message' => 'Education step saved successfully',
                    'data' => new TutorResource($tutor)
                ], 200);
            }

            if ($request->has('education')) {
                foreach ($request->education as $index => $eduData) {
                    $filePath = null;
                    
                    if ($request->hasFile("education.$index.diplomaFile")) {
                        $filePath = $request->file("education.$index.diplomaFile")
                            ->store('tutors/diplomas', 'public');
                    }

                    TutorEducation::create([
                        'tutor_id' => $tutor->id,
                        'university' => $eduData['university'] ?? null,
                        'degree' => $eduData['degree'] ?? null,
                        'degree_type' => $eduData['degreeType'] ?? null,
                        'specialization' => $eduData['specialization'] ?? null,
                        'year_from' => $eduData['yearsFrom'] ?? null,
                        'year_to' => $eduData['yearsTo'] ?? null,
                        'diploma_file' => $filePath,
                        'verification_status' => 'pending'
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Education saved successfully',
                'data' => new TutorResource($tutor)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error saving education:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error saving education',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveDescriptionStep(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tutorId' => 'required|exists:tutors,id',
                'description' => 'required|string|max:400'
            ]);

            $tutor = Tutor::findOrFail($request->tutorId);
            
            $tutor->update([
                'description' => $request->description
            ]);

            Log::info('Description saved successfully:', ['id' => $tutor->id]);

            return response()->json([
                'success' => true,
                'message' => 'Description saved successfully',
                'data' => new TutorResource($tutor)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error saving description:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error saving description',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveVideoStep(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tutorId' => 'required|exists:tutors,id',
                'introVideo' => 'nullable|file|mimes:mp4,mov,avi|max:102400',
                'videoLink' => 'nullable|url',
                'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
            ]);

            $tutor = Tutor::findOrFail($request->tutorId);

            $videoPath = null;
            $thumbnailPath = null;

            if ($request->hasFile('introVideo')) {
                if ($tutor->intro_video) {
                    Storage::disk('public')->delete($tutor->intro_video);
                }
                $videoPath = $request->file('introVideo')->store('tutors/videos', 'public');
            }

            if ($request->hasFile('thumbnail')) {
                if ($tutor->video_thumbnail) {
                    Storage::disk('public')->delete($tutor->video_thumbnail);
                }
                $thumbnailPath = $request->file('thumbnail')->store('tutors/thumbnails', 'public');
            }

            $tutor->update([
                'intro_video' => $videoPath ?? $tutor->intro_video,
                'video_link' => $request->videoLink ?? $tutor->video_link,
                'video_thumbnail' => $thumbnailPath ?? $tutor->video_thumbnail
            ]);

            Log::info('Video saved successfully:', ['id' => $tutor->id]);

            return response()->json([
                'success' => true,
                'message' => 'Video saved successfully',
                'data' => new TutorResource($tutor)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error saving video:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error saving video',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function saveAvailabilityStep(Request $request): JsonResponse
    {
        try {
            Log::info('Availability request:', $request->all());

            $validated = $request->validate([
                'tutorId' => 'required|exists:tutors,id',
                'timezone' => 'nullable|string',
                'availability' => 'nullable|array'
            ]);

            $tutor = Tutor::findOrFail($request->tutorId);
            
            // Supprimer les anciennes disponibilités
            $tutor->availability()->delete();

            // Sauvegarder timezone
            if ($request->timezone) {
                $tutor->update(['timezone' => $request->timezone]);
            }

            // Create availability from array structure [{ day, slots }]
            if ($request->has('availability') && is_array($request->availability)) {

                foreach ($request->availability as $dayData) {

                    if (
                        !isset($dayData['day']) ||
                        !isset($dayData['slots']) ||
                        !is_array($dayData['slots'])
                    ) {
                        continue;
                    }

                    $day = strtolower($dayData['day']);

                    foreach ($dayData['slots'] as $slot) {

                        $from = $slot['from'] ?? null;
                        $to   = $slot['to'] ?? null;

                        // only save valid slots
                        if (!empty($from) && !empty($to)) {

                            TutorAvailability::create([
                                'tutor_id'    => $tutor->id,
                                'timezone'    => $request->timezone,
                                'day_of_week' => $day,
                                'start_time'  => $from,
                                'end_time'    => $to
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Availability saved successfully',
                'data' => new TutorResource($tutor)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error saving availability:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error saving availability',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function savePricingStep(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tutorId' => 'required|exists:tutors,id',
                'hourlyRate' => 'required|numeric|min:1|max:200'
            ]);

            $tutor = Tutor::findOrFail($request->tutorId);
            
            $tutor->update([
                'hourly_rate' => $request->hourlyRate,
                'currency' => 'MAD'
            ]);

            Log::info('Pricing saved successfully:', ['id' => $tutor->id]);

            return response()->json([
                'success' => true,
                'message' => 'Pricing saved successfully',
                'data' => new TutorResource($tutor)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error saving pricing:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error saving pricing',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function submitProfile(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'tutorId' => 'required|exists:tutors,id'
            ]);

            $tutor = Tutor::findOrFail($request->tutorId);
            
            $tutor->update([
                'status' => 'pending',
                'submitted_at' => now()
            ]);

            Log::info('Profile submitted for approval:', ['id' => $tutor->id]);

            return response()->json([
                'success' => true,
                'message' => 'Profile submitted successfully',
                'data' => new TutorResource($tutor)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error submitting profile:', ['message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Error submitting profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}