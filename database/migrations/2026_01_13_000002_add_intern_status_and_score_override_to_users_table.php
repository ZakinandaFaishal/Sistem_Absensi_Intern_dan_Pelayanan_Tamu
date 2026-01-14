<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('intern_status')->default('aktif')->after('active');
            $table->unsignedInteger('score_override')->nullable()->after('intern_status');
            $table->string('score_override_note')->nullable()->after('score_override');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['intern_status', 'score_override', 'score_override_note']);
        });
    }
};
