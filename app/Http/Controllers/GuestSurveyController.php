<?php

namespace App\Http\Controllers;

use App\Models\GuestSurvey;
use App\Models\GuestVisit;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuestSurveyController extends Controller
{
    public function show(GuestVisit $visit)
    {
        abort_if($visit->completed_at === null, 404);

        if ($visit->survey()->exists()) {
            return view('guest.survey_thanks');
        }

        return view('guest.survey', [
            'visit' => $visit,
        ]);
    }

    public function store(Request $request, GuestVisit $visit)
    {
        abort_if($visit->completed_at === null, 404);
        abort_if($visit->survey()->exists(), 409);

        $validated = $request->validate([
            'rating' => ['required', Rule::in([1, 2, 3, 4, 5])],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        GuestSurvey::query()->create([
            'visit_id' => $visit->id,
            'rating' => (int) $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'submitted_at' => CarbonImmutable::now(),
        ]);

        return view('guest.survey_thanks');
    }
}
