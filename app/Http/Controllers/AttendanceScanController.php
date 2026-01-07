<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Support\KioskToken;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceScanController extends Controller
{
    public function show(Request $request)
    {
        $token = (string) $request->query('k', '');
        if ($token === '') {
            return redirect()
                ->route('attendance.qr')
                ->withErrors(['k' => 'Silakan scan QR absensi terlebih dahulu.']);
        }

        $claims = KioskToken::validate($token);
        if ($claims === null) {
            return redirect()
                ->route('attendance.qr')
                ->withErrors(['k' => 'QR tidak valid atau sudah kedaluwarsa. Silakan scan ulang.']);
        }

        return view('attendance.scan', [
            'token' => $token,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'k' => ['required', 'string'],
            'action' => ['required', Rule::in(['in', 'out'])],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'accuracy_m' => ['nullable', 'numeric', 'min:0'],
        ]);

        $claims = KioskToken::validate($validated['k']);
        if ($claims === null) {
            return back()->withErrors(['k' => 'QR sudah tidak berlaku. Silakan scan ulang.']);
        }

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
                'check_in_at' => $now,
                'location_id' => null,
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'accuracy_m' => $validated['accuracy_m'] ?? null,
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
                'check_out_at' => $now,
                'location_id' => null,
                'lat' => $validated['lat'],
                'lng' => $validated['lng'],
                'accuracy_m' => $validated['accuracy_m'] ?? null,
            ])->save();
        }

        return view('attendance.success', [
            'action' => $validated['action'],
        ]);
    }
}
