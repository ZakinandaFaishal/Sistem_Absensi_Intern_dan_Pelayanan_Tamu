<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('guest_visits', function (Blueprint $table) {
            $table->string('visit_type', 10)->default('single')->after('name'); // single|group
            $table->unsignedSmallInteger('group_count')->nullable()->after('visit_type');
            $table->json('group_names')->nullable()->after('group_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guest_visits', function (Blueprint $table) {
            //
        });
    }
};
