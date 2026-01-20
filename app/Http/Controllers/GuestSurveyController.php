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
        // Proteksi: hanya layanan
        if ($visit->service_type !== 'layanan') {
            abort(403, 'Survey hanya tersedia untuk kunjungan jenis layanan.');
        }

        // Cegah isi survey dua kali
        if ($visit->survey()->exists()) {
            return redirect()
                ->route('guest.thanks', $visit)
                ->with('status', 'Survey sudah diisi. Terima kasih.');
        }

        return view('guest.survey', compact('visit'));
    }

    public function store(Request $request, GuestVisit $visit)
    {
        // Proteksi: hanya layanan
        if ($visit->service_type !== 'layanan') {
            abort(403, 'Survey hanya tersedia untuk kunjungan jenis layanan.');
        }

        // Cegah double submit
        if ($visit->survey()->exists()) {
            return redirect()
                ->route('guest.thanks', $visit)
                ->with('status', 'Survey sudah diisi.');
        }

        $validated = $request->validate([
            'q1' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'q2' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'q3' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'q4' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'q5' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'q6' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'q7' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'q8' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'q9' => ['required', 'integer', Rule::in([1, 2, 3, 4])],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $scores = collect([
            $validated['q1'],
            $validated['q2'],
            $validated['q3'],
            $validated['q4'],
            $validated['q5'],
            $validated['q6'],
            $validated['q7'],
            $validated['q8'],
            $validated['q9'],
        ])->map(fn ($v) => (int) $v);

        $rating = (int) round($scores->avg());
        $rating = max(1, min(4, $rating));

        GuestSurvey::create([
            'visit_id' => $visit->id,
            'rating' => $rating,
            'q1' => (int) $validated['q1'],
            'q2' => (int) $validated['q2'],
            'q3' => (int) $validated['q3'],
            'q4' => (int) $validated['q4'],
            'q5' => (int) $validated['q5'],
            'q6' => (int) $validated['q6'],
            'q7' => (int) $validated['q7'],
            'q8' => (int) $validated['q8'],
            'q9' => (int) $validated['q9'],
            'comment' => $validated['comment'] ?? null,
            'submitted_at' => CarbonImmutable::now(),
        ]);

        //  otomatis tandai kunjungan selesai
        $visit->update([
            'completed_at' => CarbonImmutable::now(),
        ]);

        return view('guest.survey_thanks');
    }
}
