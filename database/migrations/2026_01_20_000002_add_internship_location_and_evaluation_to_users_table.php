<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('internship_location_id')
                ->nullable()
                ->after('internship_end_date')
                ->constrained('locations')
                ->nullOnDelete();

            $table->json('final_evaluation')->nullable()->after('score_override_note');
            $table->timestamp('final_evaluation_at')->nullable()->after('final_evaluation');

            $table->string('certificate_signatory_name')->nullable()->after('final_evaluation_at');
            $table->string('certificate_signatory_title')->nullable()->after('certificate_signatory_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('internship_location_id');
            $table->dropColumn([
                'final_evaluation',
                'final_evaluation_at',
                'certificate_signatory_name',
                'certificate_signatory_title',
            ]);
        });
    }
};
