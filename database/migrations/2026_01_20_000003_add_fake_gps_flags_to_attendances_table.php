<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_fake_gps')->default(false)->after('accuracy_m');
            $table->foreignId('fake_gps_flagged_by')
                ->nullable()
                ->after('is_fake_gps')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('fake_gps_flagged_at')->nullable()->after('fake_gps_flagged_by');
            $table->string('fake_gps_note')->nullable()->after('fake_gps_flagged_at');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fake_gps_flagged_by');
            $table->dropColumn(['is_fake_gps', 'fake_gps_flagged_at', 'fake_gps_note']);
        });
    }
};
