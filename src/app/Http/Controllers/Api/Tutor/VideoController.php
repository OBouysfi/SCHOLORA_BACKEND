<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Http\Requests\VideoRequest;
use App\Http\Resources\VideoResource;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{
    /**
     * Upload tutor intro video, thumbnail or save video link
     */
    public function updateVideo(VideoRequest $request, $tutorId)
    {
        $tutor = Tutor::findOrFail($tutorId);

        // Handle intro video upload
        if ($request->hasFile('intro_video')) {
            if ($tutor->intro_video && Storage::disk('public')->exists($tutor->intro_video)) {
                Storage::disk('public')->delete($tutor->intro_video);
            }

            $tutor->intro_video = $request->file('intro_video')->store('tutors/videos', 'public');
        }

        // Handle video thumbnail upload
        if ($request->hasFile('thumbnail')) {
            if ($tutor->video_thumbnail && Storage::disk('public')->exists($tutor->video_thumbnail)) {
                Storage::disk('public')->delete($tutor->video_thumbnail);
            }

            $tutor->video_thumbnail = $request->file('thumbnail')->store('tutors/thumbnails', 'public');
        }

        // Handle video link
        if ($request->filled('video_link')) {
            $tutor->video_link = $request->video_link;
        }

        $tutor->save();

        return new VideoResource($tutor);
    }
}
