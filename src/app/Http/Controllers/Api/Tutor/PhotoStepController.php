<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\PhotoStepRequest;
use App\Http\Resources\PhotoStepResource;
use App\Models\Tutor;
use Illuminate\Support\Facades\Storage;

class PhotoStepController extends Controller
{
    /**
     * Create a new Tutor with profile photo.
     */
   public function store_photo(PhotoStepRequest $request)
{
    $tutor = Tutor::findOrFail($request->tutor_id);

     // Update names

    if ($request->hasFile('photo')) {
        $path = $request->file('photo')->store('tutor_photos', 'public');
        $tutor->profile_photo = $path;
        $tutor->save();
    }

    return response()->json([
        'message' => 'Profile photo updated successfully',
        'profile_photo' => Storage::url($tutor->profile_photo),
    ]);
}
}
