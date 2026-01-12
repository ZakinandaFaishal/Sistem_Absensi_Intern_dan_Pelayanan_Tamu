<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\GuestVisit;
use App\Models\GuestSurvey;
use App\Models\User;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $intern_present_today = Attendance::whereDate('created_at', $today)
            ->whereNotNull('check_in_at')
            ->distinct('user_id')
            ->count('user_id');

        $intern_open_today = Attendance::whereDate('created_at', $today)
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->distinct('user_id')
            ->count('user_id');

        $stats = [
            'attendance_today' => $intern_present_today,
            'intern_open'      => $intern_open_today,
            'guest_today'      => GuestVisit::whereDate('created_at', $today)->count(),
            'survey_today'     => GuestSurvey::whereDate('created_at', $today)->count(),
            'users_total'      => User::count(),
        ];

        $chart = collect(range(6, 0))->map(function ($i) {
            $date = Carbon::today()->subDays($i);

            return [
                'date'   => $date->format('d M'),
                'guest'  => GuestVisit::whereDate('created_at', $date)->count(),
                'survey' => GuestSurvey::whereDate('created_at', $date)->count(),
            ];
        });

        return view('dashboard', compact('stats', 'chart'));
    }
}
