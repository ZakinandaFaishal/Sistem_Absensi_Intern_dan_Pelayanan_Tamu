<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Sync existing "Lokasi / Dinas" (locations) into the master "dinas" table.
        // This is designed for the current data model where each location row represents a dinas.
        // Idempotent: can be safely re-run.

        if (!Schema::hasTable('locations') || !Schema::hasTable('dinas')) {
            return;
        }

        $locations = DB::table('locations')
            ->select(['id', 'name', 'code', 'dinas_id'])
            ->orderBy('id')
            ->get();

        foreach ($locations as $loc) {
            $name = trim((string) ($loc->name ?? ''));
            $code = $loc->code !== null ? trim((string) $loc->code) : null;

            if ($name === '') {
                continue;
            }

            // Prefer matching by code (if present), else by name.
            $existing = null;
            if ($code !== null && $code !== '') {
                $existing = DB::table('dinas')->where('code', $code)->first();
            }
            if ($existing === null) {
                $existing = DB::table('dinas')->where('name', $name)->first();
            }

            $dinasId = $existing?->id;
            if (!$dinasId) {
                $dinasId = DB::table('dinas')->insertGetId([
                    'name' => $name,
                    'code' => ($code !== '' ? $code : null),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ((int) ($loc->dinas_id ?? 0) !== (int) $dinasId) {
                DB::table('locations')
                    ->where('id', $loc->id)
                    ->update([
                        'dinas_id' => $dinasId,
                        'updated_at' => now(),
                    ]);
            }
        }
    }

    public function down(): void
    {
        // No-op: we can't safely revert without losing intent.
    }
};
