<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Newsletter;
use App\Http\Requests\NewsletterRequest;
use App\Http\Resources\NewsletterResource;

class NewsletterController extends Controller
{
    /**
     * Store a new email in the newsletter.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreNewsletterRequest $request)
    {
        $newsletter = Newsletter::create([
            'email' => $request->email
        ]);

        return response()->json([
            'message' => 'Email added successfully!',
            'data' => new NewsletterResource($newsletter)
        ], 201);
    }
}
