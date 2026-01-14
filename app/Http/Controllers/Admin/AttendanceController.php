<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Setting;
use App\Support\AppSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $dateFrom = (string) $request->query('date_from', '');
        $dateTo = (string) $request->query('date_to', '');
        $sort = (string) $request->query('sort', 'date');
        $dir = (string) $request->query('dir', 'desc');
        $dir = in_array($dir, ['asc', 'desc'], true) ? $dir : 'desc';

        $baseQuery = Attendance::query()
            ->select('attendances.*')
            ->with(['user', 'location']);

        if ($q !== '') {
            $baseQuery->whereHas('user', function ($userQuery) use ($q) {
                $userQuery->where('name', 'like', '%' . $q . '%');
            });
        }

        if ($dateFrom !== '') {
            $baseQuery->whereDate('date', '>=', $dateFrom);
        }
        if ($dateTo !== '') {
            $baseQuery->whereDate('date', '<=', $dateTo);
        }

        if ($sort === 'name') {
            $baseQuery
                ->join('users', 'users.id', '=', 'attendances.user_id')
                ->orderBy('users.name', $dir)
                ->orderByDesc('date')
                ->orderByDesc('check_in_at');
        } else {
            $baseQuery
                ->orderBy('date', $dir)
                ->orderByDesc('check_in_at');
        }

        $attendances = $baseQuery
            ->paginate(20)
            ->withQueryString();

        $settings = [
            'office_lat' => AppSettings::getString(AppSettings::OFFICE_LAT, ''),
            'office_lng' => AppSettings::getString(AppSettings::OFFICE_LNG, ''),
            'radius_m' => AppSettings::getInt(AppSettings::RADIUS_M, 50),
            'max_accuracy_m' => AppSettings::getInt(AppSettings::MAX_ACCURACY_M, 50),
            'checkin_start' => AppSettings::getString(AppSettings::CHECKIN_START, '08:00'),
            'checkin_end' => AppSettings::getString(AppSettings::CHECKIN_END, '12:00'),
            'checkout_start' => AppSettings::getString(AppSettings::CHECKOUT_START, '13:00'),
            'checkout_end' => AppSettings::getString(AppSettings::CHECKOUT_END, '16:30'),
        ];

        return view('admin.attendance.index', [
            'attendances' => $attendances,
            'filters' => [
                'q' => $q,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sort' => $sort,
                'dir' => $dir,
            ],
            'settings' => $settings,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'office_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'office_lng' => ['nullable', 'numeric', 'between:-180,180'],
            // Strict: radius maksimum 50m.
            'radius_m' => ['required', 'integer', 'min:1', 'max:50'],
            'max_accuracy_m' => ['required', 'integer', 'min:1', 'max:5000'],
            'checkin_start' => ['required', 'date_format:H:i'],
            'checkin_end' => ['required', 'date_format:H:i'],
            'checkout_start' => ['required', 'date_format:H:i'],
            'checkout_end' => ['required', 'date_format:H:i'],
        ]);

        Setting::setValue(AppSettings::OFFICE_LAT, $validated['office_lat'] !== null ? (string) $validated['office_lat'] : '');
        Setting::setValue(AppSettings::OFFICE_LNG, $validated['office_lng'] !== null ? (string) $validated['office_lng'] : '');
        Setting::setValue(AppSettings::RADIUS_M, (string) $validated['radius_m']);
        Setting::setValue(AppSettings::MAX_ACCURACY_M, (string) $validated['max_accuracy_m']);
        Setting::setValue(AppSettings::CHECKIN_START, (string) $validated['checkin_start']);
        Setting::setValue(AppSettings::CHECKIN_END, (string) $validated['checkin_end']);
        Setting::setValue(AppSettings::CHECKOUT_START, (string) $validated['checkout_start']);
        Setting::setValue(AppSettings::CHECKOUT_END, (string) $validated['checkout_end']);

        return back()->with('status', 'Aturan presensi berhasil diperbarui.');
    }
}
