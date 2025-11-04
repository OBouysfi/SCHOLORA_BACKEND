<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Resources\Tutor\TutorResource;
use App\Models\Tutor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TutorRegistrationController extends Controller
{
    public function saveAboutStep(Request $request): JsonResponse
    {
        try {
            Log::info('Request received:', $request->all());

            $validated = $request->validate([
                'firstName' => 'required|string|min:2|max:50',
                'lastName' => 'required|string|min:2|max:50',
                'email' => 'required|email',
                'country' => 'required|string',
                'subject' => 'required|string',
                'phone' => 'nullable|string',
                'isOver18' => 'required'
            ]);

            Log::info('Validation passed');

            // Vérifier si l'email existe déjà
            $existingTutor = Tutor::where('email', $request->email)->first();

            // Si un ID est passé dans la requête, c'est une mise à jour
            $isUpdate = $request->has('id') && $request->id;

            if ($existingTutor) {
                // Si le tutor existe et que c'est une mise à jour du même tutor
                if ($isUpdate && $existingTutor->id == $request->id) {
                    Log::info('Updating existing tutor:', ['id' => $existingTutor->id]);
                    $existingTutor->update([
                        'first_name' => $request->firstName,
                        'last_name' => $request->lastName,
                        'country' => $request->country,
                        'main_subject' => $request->subject,
                        'phone' => $request->phone,
                        'is_over_18' => $request->isOver18 ? true : false,
                    ]);
                    $tutor = $existingTutor;
                } else {
                    // Email existe déjà pour un autre tutor
                    return response()->json([
                        'success' => false,
                        'message' => 'This email is already registered',
                        'errors' => [
                            'email' => ['This email is already registered']
                        ]
                    ], 422);
                }
            } else {
                // Création d'un nouveau tutor
                Log::info('Creating new tutor');
                $tutor = Tutor::create([
                    'first_name' => $request->firstName,
                    'last_name' => $request->lastName,
                    'email' => $request->email,
                    'country' => $request->country,
                    'main_subject' => $request->subject,
                    'phone' => $request->phone,
                    'is_over_18' => $request->isOver18 ? true : false,
                ]);
            }

            Log::info('Tutor saved successfully:', ['id' => $tutor->id]);

            return response()->json([
                'success' => true,
                'message' => 'Profile saved successfully',
                'data' => new TutorResource($tutor)
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error saving profile:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error saving profile',
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
}