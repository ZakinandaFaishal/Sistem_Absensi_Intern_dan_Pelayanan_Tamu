<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_surveys', function (Blueprint $table) {
            $table->unsignedTinyInteger('q1')->nullable()->after('rating');
            $table->unsignedTinyInteger('q2')->nullable()->after('q1');
            $table->unsignedTinyInteger('q3')->nullable()->after('q2');
            $table->unsignedTinyInteger('q4')->nullable()->after('q3');
            $table->unsignedTinyInteger('q5')->nullable()->after('q4');
            $table->unsignedTinyInteger('q6')->nullable()->after('q5');
            $table->unsignedTinyInteger('q7')->nullable()->after('q6');
            $table->unsignedTinyInteger('q8')->nullable()->after('q7');
            $table->unsignedTinyInteger('q9')->nullable()->after('q8');
        });
    }

    public function down(): void
    {
        Schema::table('guest_surveys', function (Blueprint $table) {
            $table->dropColumn(['q1', 'q2', 'q3', 'q4', 'q5', 'q6', 'q7', 'q8', 'q9']);
        });
    }
};
