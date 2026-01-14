<?php

namespace App\Support;

use App\Models\Setting;

final class AppSettings
{
    public const OFFICE_LAT = 'attendance.office_lat';
    public const OFFICE_LNG = 'attendance.office_lng';
    public const RADIUS_M = 'attendance.radius_m';
    public const MAX_ACCURACY_M = 'attendance.max_accuracy_m';

    public const CHECKIN_START = 'attendance.checkin_start'; // HH:MM
    public const CHECKIN_END = 'attendance.checkin_end';
    public const CHECKOUT_START = 'attendance.checkout_start';
    public const CHECKOUT_END = 'attendance.checkout_end';

    public const SCORE_POINTS_PER_ATTENDANCE = 'scoring.points_per_attendance';
    public const SCORE_MAX = 'scoring.max_score';

    public static function getString(string $key, string $default = ''): string
    {
        $value = Setting::getValue($key);
        if ($value === null || $value === '') {
            return $default;
        }
        return $value;
    }

    public static function getInt(string $key, int $default = 0): int
    {
        $value = Setting::getValue($key);
        if ($value === null || $value === '') {
            return $default;
        }
        return (int) $value;
    }

    public static function getFloat(string $key, float $default = 0.0): float
    {
        $value = Setting::getValue($key);
        if ($value === null || $value === '') {
            return $default;
        }
        return (float) $value;
    }
}
