<?php
namespace App\Http\Controllers\Api\Tutor;

use App\Http\Controllers\Controller;
use App\Http\Requests\AboutStepRequest;
use App\Http\Resources\AboutStepResource;
use App\Models\Tutor;
use App\Models\TutorLanguage;
use App\Models\TutorSubject;
use App\Models\TutorRegistrationStep;
use Illuminate\Support\Facades\DB;

class AboutStepController extends Controller
{
    public function store(AboutStepRequest $request)
    {
        return DB::transaction(function () use ($request) {
            /* Create Tutor About Step */
            $tutor = Tutor::create([
                'first_name'  => $request->first_name,
                'last_name'   => $request->last_name,
                'email'       => $request->email,
                'country'     => $request->country,
                'phone'       => $request->phone,
                'is_over_18'  => $request->is_over_18,
                'status'      => 'draft',
            ]);

            /* Save Subject */
            TutorSubject::create([
                'tutor_id'   => $tutor->id,
                'subject'    => $request->subject,
                'is_primary' => true,
            ]);

            /* Save Languages */
            foreach ($request->languages as $lang) {
                TutorLanguage::create([
                    'tutor_id' => $tutor->id,
                    'language' => $lang['language'],
                    'level'    => $lang['level'],
                ]);
            }

            /* Save Progress */
            TutorRegistrationStep::updateOrCreate(
                ['tutor_id' => $tutor->id, 'step_name' => 'about'],
                ['status' => 'complete']
            );

            return new AboutStepResource($tutor);
        });
    }

    public function update(AboutStepRequest $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            /* Find Tutor */
            $tutor = Tutor::findOrFail($id);

            /* Update Tutor About Step */
            $tutor->update([
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email'      => $request->email,
                'country'    => $request->country,
                'phone'      => $request->phone,
                'is_over_18' => $request->is_over_18,
            ]);

            /* Update Subject (replace old primary subject) */
            TutorSubject::updateOrCreate(
                ['tutor_id' => $tutor->id, 'is_primary' => true],
                ['subject' => $request->subject]
            );

            /* Update Languages: remove old + insert new */
            TutorLanguage::where('tutor_id', $tutor->id)->delete();
            foreach ($request->languages as $lang) {
                TutorLanguage::create([
                    'tutor_id' => $tutor->id,
                    'language' => $lang['language'],
                    'level'    => $lang['level'],
                ]);
            }

            /* Update Progress */
            TutorRegistrationStep::updateOrCreate(
                ['tutor_id' => $tutor->id, 'step_name' => 'about'],
                ['status' => 'complete']
            );

            return new AboutStepResource($tutor);
        });
    }

}
