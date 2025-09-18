<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\DescriptionRequest;
use App\Http\Resources\DescriptionResource;
use App\Models\Tutor;

class DescriptionController extends Controller
{
    /**
     * Store or update the tutor's description
     */
    public function store_description(DescriptionRequest $request)
    {
    
        $tutor = Tutor::findOrFail($request->tutor_id);

        $tutor->description = $request->description;
        $tutor->save();

        return new DescriptionResource($tutor);
    }
}
