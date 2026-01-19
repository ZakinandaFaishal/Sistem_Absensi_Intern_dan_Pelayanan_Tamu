<?php

namespace App\Support;

final class Ikm
{
    /**
     * Convert NRR (1..4 scale) to IKM (25..100 scale).
     */
    public static function nrrToIkm(float $nrr): float
    {
        return $nrr * 25.0;
    }

    /**
     * Determine mutu/kinerja category based on PermenPANRB No. 14 Tahun 2017.
     *
     * Common intervals used in the regulation:
     * - 88.31 - 100.00 => A (Sangat Baik)
     * - 76.61 -  88.30 => B (Baik)
     * - 65.00 -  76.60 => C (Kurang Baik)
     * - 25.00 -  64.99 => D (Tidak Baik)
     */
    public static function category(float $ikm): array
    {
        if ($ikm >= 88.31) {
            return ['mutu' => 'A', 'kinerja' => 'Sangat Baik'];
        }
        if ($ikm >= 76.61) {
            return ['mutu' => 'B', 'kinerja' => 'Baik'];
        }
        if ($ikm >= 65.00) {
            return ['mutu' => 'C', 'kinerja' => 'Kurang Baik'];
        }

        return ['mutu' => 'D', 'kinerja' => 'Tidak Baik'];
    }

    /**
     * Compute overall NRR and IKM from an array of averages per unsur (q1..q9).
     * Values may be null when older records exist.
     */
    public static function fromAverages(array $avgByKey): array
    {
        $vals = [];
        foreach ($avgByKey as $v) {
            if ($v === null) {
                continue;
            }
            $vals[] = (float) $v;
        }

        $nrr = count($vals) ? (array_sum($vals) / count($vals)) : 0.0;
        $ikm = self::nrrToIkm($nrr);
        $cat = self::category($ikm);

        return [
            'nrr' => $nrr,
            'ikm' => $ikm,
            'mutu' => $cat['mutu'],
            'kinerja' => $cat['kinerja'],
        ];
    }
}
