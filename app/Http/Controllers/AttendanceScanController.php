<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Location;
use App\Support\KioskToken;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceScanController extends Controller
{
    public function show(Request $request)
    {
        $token = (string) $request->query('k', '');
        $claims = $token !== '' ? KioskToken::validate($token) : null;
        if ($claims === null) {
            abort(404);
        }

        $location = Location::query()->findOrFail($claims['loc']);

        return view('attendance.scan', [
            'token' => $token,
            'location' => $location,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'k' => ['required', 'string'],
            'action' => ['required', Rule::in(['in', 'out'])],
        ]);

        $claims = KioskToken::validate($validated['k']);
        if ($claims === null) {
            return back()->withErrors(['k' => 'QR sudah tidak berlaku. Silakan scan ulang.']);
        }

        $locationId = (int) $claims['loc'];
        $userId = (int) $request->user()->id;

        $today = CarbonImmutable::now()->toDateString();
        $now = CarbonImmutable::now();

        $attendance = Attendance::query()
            ->where('user_id', $userId)
            ->whereDate('date', $today)
            ->first();

        if ($validated['action'] === 'in') {
            if ($attendance && $attendance->check_in_at !== null) {
                return back()->withErrors(['action' => 'Anda sudah check-in hari ini.']);
            }

            $attendance ??= Attendance::query()->create([
                'user_id' => $userId,
                'date' => $today,
            ]);

            $attendance->fill([
                'location_id' => $locationId,
                'check_in_at' => $now,
            ])->save();
        }

        if ($validated['action'] === 'out') {
            if (!$attendance || $attendance->check_in_at === null) {
                return back()->withErrors(['action' => 'Belum ada check-in hari ini.']);
            }

            if ($attendance->check_out_at !== null) {
                return back()->withErrors(['action' => 'Anda sudah check-out hari ini.']);
            }

            $attendance->fill([
                'location_id' => $locationId,
                'check_out_at' => $now,
            ])->save();
        }

        return view('attendance.success', [
            'action' => $validated['action'],
        ]);
    }
}
