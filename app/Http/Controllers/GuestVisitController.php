<?php

namespace App\Http\Controllers;

use App\Models\GuestVisit;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class GuestVisitController extends Controller
{
    public function create()
    {
        return view('guest.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'institution' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'purpose' => ['required', 'string', 'max:255'],
        ]);

        GuestVisit::query()->create([
            ...$validated,
            'arrived_at' => CarbonImmutable::now(),
        ]);

        return view('guest.thanks');
    }

    public function index()
    {
        $visits = GuestVisit::query()->latest('arrived_at')->limit(50)->get();

        return view('admin.guest.index', [
            'visits' => $visits,
        ]);
    }

    public function complete(Request $request, GuestVisit $visit)
    {
        if ($visit->completed_at !== null) {
            return redirect()->route('guest.survey.show', $visit);
        }

        $visit->fill([
            'completed_at' => CarbonImmutable::now(),
            'handled_by' => $request->user()->id,
        ])->save();

        return redirect()->route('guest.survey.show', $visit);
    }
}
