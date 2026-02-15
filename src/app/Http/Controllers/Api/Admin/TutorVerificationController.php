<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TutorVerificationController extends Controller
{
    /**
     * List tutor applications with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Tutor::with('registrationSteps')
            ->whereIn('status', ['pending', 'approved', 'rejected']);

        // ── Filters ──────────────────────────────────────────
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'ILIKE', "%{$search}%")
                  ->orWhere('last_name', 'ILIKE', "%{$search}%")
                  ->orWhere('email', 'ILIKE', "%{$search}%")
                  ->orWhere('main_subject', 'ILIKE', "%{$search}%");
            });
        }

        if ($request->filled('subject')) {
            $query->where('main_subject', 'ILIKE', "%{$request->subject}%");
        }

        // ── Sorting ──────────────────────────────────────────
        switch ($request->get('sort_by', 'newest')) {
            case 'oldest':
                $query->orderBy('submitted_at', 'asc');
                break;
            case 'rate_high':
                $query->orderBy('hourly_rate', 'desc');
                break;
            case 'rate_low':
                $query->orderBy('hourly_rate', 'asc');
                break;
            default:
                $query->orderBy('submitted_at', 'desc');
                break;
        }

        $tutors = $query->get();

        $transformed = $tutors->map(function ($tutor) {
            return [
                'id'              => $tutor->id,
                'first_name'      => $tutor->first_name,
                'last_name'       => $tutor->last_name,
                'email'           => $tutor->email,
                'phone'           => $tutor->phone ?? '',
                'country'         => $tutor->country ?? '',
                'main_subject'    => $tutor->main_subject ?? '',
                'description'     => $tutor->description ?? '',
                'hourly_rate'     => $tutor->hourly_rate,
                'currency'        => $tutor->currency,
                'profile_photo'   => $tutor->profile_photo ? asset('storage/' . $tutor->profile_photo) : null,
                'intro_video'     => $tutor->intro_video ? asset('storage/' . $tutor->intro_video) : null,
                'video_link'      => $tutor->video_link,
                'status'          => $tutor->status,
                'submitted_at'    => $tutor->submitted_at?->toDateString(),
                'approved_at'     => $tutor->approved_at?->toDateString(),
                'rejection_reason'=> $tutor->rejection_reason,
                'is_over_18'      => $tutor->is_over_18,
                'total_hours'     => $tutor->total_hours,
                'average_rating'  => $tutor->average_rating,
                'total_reviews'   => $tutor->total_reviews,
                'steps'           => $tutor->registrationSteps->map(function ($step) {
                    return [
                        'step_name'    => $step->step_name,
                        'status'       => $step->status,
                        'completed_at' => $step->completed_at,
                    ];
                }),
            ];
        });

        return response()->json(['data' => $transformed]);
    }

    /**
     * Verification statistics
     */
    public function stats(): JsonResponse
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        $stats = [
            'pending_review'      => Tutor::where('status', 'pending')->count(),
            'approved_this_month' => Tutor::where('status', 'approved')
                                        ->where('approved_at', '>=', $startOfMonth)->count(),
            'rejected_this_month' => Tutor::where('status', 'rejected')
                                        ->where('updated_at', '>=', $startOfMonth)->count(),
            'total_tutors'        => Tutor::where('status', 'approved')->count(),
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Show single tutor detail
     */
    public function show(int $id): JsonResponse
    {
        $tutor = Tutor::with('registrationSteps')->findOrFail($id);

        return response()->json(['data' => $tutor]);
    }

    /**
     * Update tutor status (approve / reject)
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,pending',
            'reason' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $tutor = Tutor::findOrFail($id);

            $updateData = [
                'status' => $request->status,
            ];

            if ($request->status === 'approved') {
                $updateData['approved_at'] = now();
                $updateData['rejection_reason'] = null;
            }

            if ($request->status === 'rejected') {
                $updateData['rejection_reason'] = $request->reason;
                $updateData['approved_at'] = null;
            }

            $tutor->update($updateData);

            // TODO: Send notification email to tutor
            // $tutor->notify(new TutorApplicationStatusNotification($tutor));

            DB::commit();

            return response()->json([
                'message' => "Tutor {$request->status} successfully.",
                'data'    => $tutor->fresh(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tutor verification error: ' . $e->getMessage());

            return response()->json([
                'message' => 'An error occurred while updating the tutor status.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request): JsonResponse
    {
        $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer|exists:tutors,id',
            'status' => 'required|in:approved,rejected,pending',
            'reason' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $tutors = Tutor::whereIn('id', $request->ids)->get();

            foreach ($tutors as $tutor) {
                $updateData = ['status' => $request->status];

                if ($request->status === 'approved') {
                    $updateData['approved_at'] = now();
                    $updateData['rejection_reason'] = null;
                }

                if ($request->status === 'rejected') {
                    $updateData['rejection_reason'] = $request->reason;
                    $updateData['approved_at'] = null;
                }

                $tutor->update($updateData);
            }

            DB::commit();

            return response()->json([
                'message' => count($request->ids) . " tutors {$request->status} successfully.",
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Bulk update failed.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify a registration step document
     */
    public function verifyDocument(Request $request, int $applicationId, int $documentId): JsonResponse
    {
        $tutor = Tutor::findOrFail($applicationId);
        $step = $tutor->registrationSteps()->findOrFail($documentId);

        $step->update([
            'status'       => 'complete',
            'completed_at' => now(),
        ]);

        return response()->json([
            'message' => 'Step verified successfully.',
            'data'    => $step->fresh(),
        ]);
    }
}