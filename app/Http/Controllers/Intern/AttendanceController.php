<?php

namespace App\Http\Controllers\Intern;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $attendances = Attendance::query()
            ->with(['location'])
            ->where('user_id', $user->id)
            ->orderByDesc('date')
            ->paginate(20)
            ->withQueryString();

        return view('intern.attendance.index', [
            'attendances' => $attendances,
        ]);
    }
}
