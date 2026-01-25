<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_visits', function (Blueprint $table) {
            if (!Schema::hasColumn('guest_visits', 'dinas_id')) {
                $table->foreignId('dinas_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('dinas')
                    ->nullOnDelete();
            }

            // Index tetap aman ditambahkan terpisah.
            $table->index('dinas_id');
        });
    }

    public function down(): void
    {
        Schema::table('guest_visits', function (Blueprint $table) {
            if (Schema::hasColumn('guest_visits', 'dinas_id')) {
                $table->dropIndex(['dinas_id']);
                $table->dropConstrainedForeignId('dinas_id');
            }
        });
    }
};
