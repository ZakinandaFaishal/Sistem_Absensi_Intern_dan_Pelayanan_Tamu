<?php

namespace App\Support;

use Illuminate\Support\Str;

final class KioskToken
{
    private const WINDOW_SECONDS = 30;
    private const SKEW_WINDOWS = 1;

    /**
     * @return array{token: string, scan_url: string, expires_in: int}
     */
    public static function issue(int $locationId, ?string $baseUrl = null): array
    {
        $now = time();
        $window = intdiv($now, self::WINDOW_SECONDS);

        $payload = [
            'loc' => $locationId,
            'w' => $window,
            'n' => Str::random(10),
        ];

        $data = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $sig = self::base64UrlEncode(hash_hmac('sha256', $data, self::signingKey(), true));
        $token = $data . '.' . $sig;

        $expiresIn = (($window + 1) * self::WINDOW_SECONDS) - $now;
        $path = '/presensi/scan?k=' . rawurlencode($token);
        $scanUrl = $baseUrl ? rtrim($baseUrl, '/') . $path : url($path);

        return [
            'token' => $token,
            'scan_url' => $scanUrl,
            'expires_in' => max(1, $expiresIn),
        ];
    }

    /**
     * @return array{loc:int}|null
     */
    public static function validate(string $token): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 2) {
            return null;
        }

        [$data, $sig] = $parts;
        $expected = self::base64UrlEncode(hash_hmac('sha256', $data, self::signingKey(), true));
        if (!hash_equals($expected, $sig)) {
            return null;
        }

        $json = self::base64UrlDecode($data);
        if ($json === null) {
            return null;
        }

        $payload = json_decode($json, true);
        if (!is_array($payload) || !isset($payload['loc'], $payload['w'])) {
            return null;
        }

        $loc = (int) $payload['loc'];
        $w = (int) $payload['w'];
        $nowW = intdiv(time(), self::WINDOW_SECONDS);

        if (abs($nowW - $w) > self::SKEW_WINDOWS) {
            return null;
        }

        return ['loc' => $loc];
    }

    private static function signingKey(): string
    {
        $key = (string) config('app.key');
        if (str_starts_with($key, 'base64:')) {
            $decoded = base64_decode(substr($key, 7), true);
            if ($decoded !== false) {
                return $decoded;
            }
        }

        return $key;
    }

    private static function base64UrlEncode(string $raw): string
    {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $encoded): ?string
    {
        $padLen = (4 - (strlen($encoded) % 4)) % 4;
        $padded = $encoded . str_repeat('=', $padLen);
        $raw = base64_decode(strtr($padded, '-_', '+/'), true);

        return $raw === false ? null : $raw;
    }
}
