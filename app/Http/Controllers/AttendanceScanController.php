<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRule;
use App\Models\Attendance;
use App\Models\Location;
use App\Support\AppSettings;
use App\Support\Geo;
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
        $user = $request->user();
        if (($user?->role ?? null) === 'intern' && ($user?->intern_status ?? 'aktif') === 'tamat') {
            return back()->withErrors(['action' => 'Status Anda sudah TAMAT. Presensi dinonaktifkan.']);
        }

        $validated = $request->validate([
            'k' => ['required', 'string'],
            'action' => ['required', Rule::in(['in', 'out'])],
            'lat' => ['required', 'numeric', 'between:-90,90'],
            'lng' => ['required', 'numeric', 'between:-180,180'],
            'accuracy_m' => ['nullable', 'numeric', 'min:0'],
        ]);

        // Enforce time windows (server time: Asia/Jakarta).
        $now = CarbonImmutable::now();
        $time = $now->format('H:i');

        $dinasIdForRules = null;
        if (($user?->role ?? null) === 'admin_dinas' && !empty($user?->dinas_id)) {
            $dinasIdForRules = (int) $user->dinas_id;
        }

        $rule = null;
        if ($dinasIdForRules !== null) {
            $rule = AttendanceRule::query()->where('dinas_id', $dinasIdForRules)->first();
        }

        $checkinStart = $rule?->checkin_start ?? AppSettings::getString(AppSettings::CHECKIN_START, '08:00');
        $checkinEnd = $rule?->checkin_end ?? AppSettings::getString(AppSettings::CHECKIN_END, '12:00');
        $checkoutStart = $rule?->checkout_start ?? AppSettings::getString(AppSettings::CHECKOUT_START, '13:00');
        $checkoutEnd = $rule?->checkout_end ?? AppSettings::getString(AppSettings::CHECKOUT_END, '16:30');

        // Geofence + accuracy threshold.
        $targetName = 'kantor';
        $targetLat = $rule?->office_lat !== null ? (float) $rule->office_lat : AppSettings::getFloat(AppSettings::OFFICE_LAT, 0.0);
        $targetLng = $rule?->office_lng !== null ? (float) $rule->office_lng : AppSettings::getFloat(AppSettings::OFFICE_LNG, 0.0);

        $targetLocationId = null;
        if (($user?->role ?? null) === 'intern' && $user?->internship_location_id) {
            $internLocation = Location::query()->find($user->internship_location_id);
            if ($internLocation === null) {
                return back()->withErrors([
                    'lat' => 'Lokasi magang Anda tidak ditemukan. Hubungi admin untuk memperbaiki penugasan lokasi.',
                ]);
            }

            if ($dinasIdForRules === null && !empty($internLocation->dinas_id)) {
                $dinasIdForRules = (int) $internLocation->dinas_id;
                $rule = AttendanceRule::query()->where('dinas_id', $dinasIdForRules)->first();

                $checkinStart = $rule?->checkin_start ?? $checkinStart;
                $checkinEnd = $rule?->checkin_end ?? $checkinEnd;
                $checkoutStart = $rule?->checkout_start ?? $checkoutStart;
                $checkoutEnd = $rule?->checkout_end ?? $checkoutEnd;

                $targetLat = $rule?->office_lat !== null ? (float) $rule->office_lat : $targetLat;
                $targetLng = $rule?->office_lng !== null ? (float) $rule->office_lng : $targetLng;
            }

            if ($internLocation->lat === null || $internLocation->lng === null) {
                return back()->withErrors([
                    'lat' => 'Koordinat lokasi magang belum diatur admin. Hubungi admin untuk melengkapi titik lokasi.',
                ]);
            }

            $targetName = (string) ($internLocation->name ?? 'lokasi magang');
            $targetLat = (float) $internLocation->lat;
            $targetLng = (float) $internLocation->lng;
            $targetLocationId = (int) $internLocation->id;
        }

        // Enforce time windows (server time: Asia/Jakarta) after resolving per-dinas rules.
        if ($validated['action'] === 'in') {
            if ($time < $checkinStart || $time > $checkinEnd) {
                return back()->withErrors([
                    'action' => "Check-in hanya diizinkan pada jam {$checkinStart} - {$checkinEnd}.",
                ]);
            }
        }

        if ($validated['action'] === 'out') {
            if ($time < $checkoutStart || $time > $checkoutEnd) {
                return back()->withErrors([
                    'action' => "Check-out hanya diizinkan pada jam {$checkoutStart} - {$checkoutEnd}.",
                ]);
            }
        }

        // Strict rule: presensi hanya boleh dalam radius 50 meter dari titik kantor.
        // Even if settings are changed, we cap radius to 50m server-side.
        $radiusM = min((int) ($rule?->radius_m ?? AppSettings::getInt(AppSettings::RADIUS_M, 50)), 50);
        $maxAccuracyM = (int) ($rule?->max_accuracy_m ?? AppSettings::getInt(AppSettings::MAX_ACCURACY_M, 50));

        $accuracy = isset($validated['accuracy_m']) ? (float) $validated['accuracy_m'] : null;
        if ($accuracy === null) {
            return back()->withErrors([
                'accuracy_m' => 'Lokasi belum siap. Pastikan GPS aktif & izin lokasi untuk browser diizinkan, lalu coba lagi.',
            ]);
        }
        if ($accuracy > $maxAccuracyM) {
            return back()->withErrors([
                'accuracy_m' => 'Sinyal lokasi belum cukup akurat. Pindah ke area terbuka, tunggu lokasi stabil, lalu coba lagi.',
            ]);
        }

        if ($targetLat === 0.0 && $targetLng === 0.0) {
            return back()->withErrors([
                'lat' => 'Titik lokasi presensi belum dikonfigurasi. Hubungi admin untuk mengatur koordinat lokasi.',
            ]);
        }

        // Enforce distance.
        if ($targetLat !== 0.0 || $targetLng !== 0.0) {
            $distanceM = Geo::distanceMeters(
                (float) $validated['lat'],
                (float) $validated['lng'],
                (float) $targetLat,
                (float) $targetLng
            );

            if ($distanceM > $radiusM) {
                $prettyDistance = $distanceM >= 1000
                    ? number_format($distanceM / 1000, 2, ',', '.') . ' km'
                    : number_format($distanceM, 0, ',', '.') . ' m';
                return back()->withErrors([
                    'lat' => "Anda berada di luar area presensi (jarak ~{$prettyDistance} dari {$targetName}). Presensi hanya bisa dalam radius {$radiusM} m dari {$targetName}.",
                ]);
            }
        }

        $claims = KioskToken::validate($validated['k']);
        if ($claims === null) {
            return back()->withErrors(['k' => 'QR sudah tidak berlaku. Silakan scan ulang.']);
        }

        $userId = (int) $request->user()->id;

        $today = $now->toDateString();
        // $now already computed above.

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
                'location_id' => $targetLocationId,
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
                'location_id' => $targetLocationId,
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
