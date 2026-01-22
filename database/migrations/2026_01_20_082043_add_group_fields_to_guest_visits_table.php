<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_visits', function (Blueprint $table) {
            if (!Schema::hasColumn('guest_visits', 'visit_type')) {
                $table->string('visit_type', 10)->default('single')->after('arrived_at');
            }

            if (!Schema::hasColumn('guest_visits', 'group_count')) {
                $table->unsignedSmallInteger('group_count')->nullable()->after('visit_type');
            }

            if (!Schema::hasColumn('guest_visits', 'group_names')) {
                $table->json('group_names')->nullable()->after('group_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guest_visits', function (Blueprint $table) {
            $columnsToDrop = [];

            if (Schema::hasColumn('guest_visits', 'group_names')) {
                $columnsToDrop[] = 'group_names';
            }

            if (Schema::hasColumn('guest_visits', 'group_count')) {
                $columnsToDrop[] = 'group_count';
            }

            if (Schema::hasColumn('guest_visits', 'visit_type')) {
                $columnsToDrop[] = 'visit_type';
            }

            if ($columnsToDrop !== []) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
