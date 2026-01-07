<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $attendances = Attendance::query()
            ->with(['user', 'location'])
            ->orderByDesc('date')
            ->orderByDesc('check_in_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.attendance.index', [
            'attendances' => $attendances,
        ]);
    }
}
