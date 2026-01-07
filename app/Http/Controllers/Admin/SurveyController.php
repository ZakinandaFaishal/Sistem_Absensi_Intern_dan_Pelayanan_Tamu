<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GuestSurvey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function index(Request $request)
    {
        $surveys = GuestSurvey::query()
            ->with(['visit'])
            ->orderByDesc('submitted_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.survey.index', [
            'surveys' => $surveys,
        ]);
    }
}
