<?php

use App\Models\Location;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function indexExists(string $table, string $indexName): bool
    {
        $rows = DB::select("SHOW INDEX FROM `{$table}`");
        foreach ($rows as $row) {
            if (($row->Key_name ?? null) === $indexName) {
                return true;
            }
        }
        return false;
    }

    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $dbName = DB::getDatabaseName();
        $rows = DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_TYPE = "FOREIGN KEY" AND CONSTRAINT_NAME = ? LIMIT 1',
            [$dbName, $table, $constraintName]
        );

        return count($rows) > 0;
    }

    public function up(): void
    {
        if (!Schema::hasColumn('attendance_rules', 'location_id')) {
            Schema::table('attendance_rules', function (Blueprint $table) {
                $table->foreignId('location_id')->nullable()->after('dinas_id');
            });
        }

        // Pastikan FK location_id ada (untuk DB yang kolomnya sudah ada tapi constraint belum).
        if (!$this->foreignKeyExists('attendance_rules', 'attendance_rules_location_id_foreign')) {
            Schema::table('attendance_rules', function (Blueprint $table) {
                $table->foreign('location_id')
                    ->references('id')
                    ->on('locations')
                    ->cascadeOnDelete();
            });
        }

        Schema::table('attendance_rules', function (Blueprint $table) {
            // Order penting: dinas_id dipakai FK, jadi siapkan index dulu sebelum drop unique.
            if (!$this->indexExists('attendance_rules', 'attendance_rules_dinas_id_index')) {
                $table->index('dinas_id');
            }

            if ($this->indexExists('attendance_rules', 'attendance_rules_dinas_id_unique')) {
                $table->dropUnique('attendance_rules_dinas_id_unique');
            }

            if (!$this->indexExists('attendance_rules', 'attendance_rules_location_id_unique')) {
                $table->unique('location_id');
            }
        });

        // Backfill: convert per-dinas rule into per-location rules.
        $rules = DB::table('attendance_rules')->select('*')->get();
        foreach ($rules as $rule) {
            $dinasId = (int) $rule->dinas_id;
            $locationIds = Location::query()
                ->where('dinas_id', $dinasId)
                ->orderBy('name')
                ->pluck('id')
                ->all();

            if (count($locationIds) === 0) {
                // No locations yet for this dinas.
                continue;
            }

            $firstLocationId = (int) $locationIds[0];

            // Assign existing row to the first location if not already used.
            $alreadyUsed = DB::table('attendance_rules')
                ->where('location_id', $firstLocationId)
                ->exists();

            if (!$alreadyUsed) {
                DB::table('attendance_rules')
                    ->where('id', (int) $rule->id)
                    ->update(['location_id' => $firstLocationId]);
            }

            // Create rule rows for remaining locations by copying values.
            foreach (array_slice($locationIds, 1) as $locId) {
                $locId = (int) $locId;
                $exists = DB::table('attendance_rules')->where('location_id', $locId)->exists();
                if ($exists) {
                    continue;
                }

                DB::table('attendance_rules')->insert([
                    'dinas_id' => $dinasId,
                    'location_id' => $locId,
                    'office_lat' => $rule->office_lat,
                    'office_lng' => $rule->office_lng,
                    'radius_m' => $rule->radius_m,
                    'max_accuracy_m' => $rule->max_accuracy_m,
                    'checkin_start' => $rule->checkin_start,
                    'checkin_end' => $rule->checkin_end,
                    'checkout_start' => $rule->checkout_start,
                    'checkout_end' => $rule->checkout_end,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('attendance_rules', function (Blueprint $table) {
            if ($this->indexExists('attendance_rules', 'attendance_rules_location_id_unique')) {
                $table->dropUnique('attendance_rules_location_id_unique');
            }

            if ($this->indexExists('attendance_rules', 'attendance_rules_dinas_id_index')) {
                $table->dropIndex('attendance_rules_dinas_id_index');
            }

            if (!$this->indexExists('attendance_rules', 'attendance_rules_dinas_id_unique')) {
                $table->unique('dinas_id');
            }

            if (Schema::hasColumn('attendance_rules', 'location_id')) {
                // dropConstrainedForeignId membutuhkan constraint ada; aman untuk MySQL yang sudah punya FK.
                $table->dropConstrainedForeignId('location_id');
            }
        });
    }
};
