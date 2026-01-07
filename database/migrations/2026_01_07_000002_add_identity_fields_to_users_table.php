<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Keep nullable to avoid breaking existing users.
            $table->string('nik', 20)->nullable()->unique()->after('name');
            $table->string('phone', 30)->nullable()->after('nik');
            $table->string('username', 50)->nullable()->unique()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nik']);
            $table->dropUnique(['username']);
            $table->dropColumn(['nik', 'phone', 'username']);
        });
    }
};
