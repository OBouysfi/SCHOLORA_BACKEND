<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Newsleter;
use App\Http\Requests\NewsleterRequest;
use App\Http\Resources\NewsleterResource;
use Illuminate\Support\Facades\Mail;



class NewsleterController extends Controller
{
    /**
     * Display a listing of the emails in the newsleter.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function index()
    {
        $newsletters = Newsleter::orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'message' => 'List of emails',
            'data' => NewsleterResource::collection($newsletters),
            'meta' => [
                'current_page' => $newsletters->currentPage(),
                'last_page' => $newsletters->lastPage(),
                'per_page' => $newsletters->perPage(),
                'total' => $newsletters->total(),
                'next_page_url' => $newsletters->nextPageUrl(),
                'prev_page_url' => $newsletters->previousPageUrl(),
            ]
        ], 200);
    }
    /**
     * Store a new email in the newsleter.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(NewsleterRequest $request)
    {
        $newsleter = Newsleter::create([
            'email' => $request->email
        ]);

        return response()->json([
            'message' => 'Email added successfully!',
            'data' => new NewsleterResource($newsleter)
        ], 201);
    }

    /**
     * Send emails to selected newsletters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmails(Request $request)
    {
        $request->validate([
            'emails' => 'required|array',
            'emails.*' => 'email',
            'message' => 'nullable|string',
            'link' => 'nullable|url',
            'file' => 'nullable|file',
        ]);

        $emails = $request->emails;
        $message = $request->message;
        $link = $request->link;
        $file = $request->file('file');

        foreach ($emails as $email) {
           Mail::send([], [], function ($m) use ($email, $message, $link, $file) {
                $content = ($message ?? '') . ($link ? "<br><a href='$link'>$link</a>" : '');

                $m->to($email)
                ->subject('Newsletter')
                ->html($content); // بدل setBody ب html()

                if ($file) {
                    $m->attach($file->getRealPath(), [
                        'as' => $file->getClientOriginalName(),
                        'mime' => $file->getMimeType(),
                    ]);
                }
            });

        }

        return response()->json([
            'success' => true,
            'message' => 'Emails sent successfully!'
        ]);
    }
}
