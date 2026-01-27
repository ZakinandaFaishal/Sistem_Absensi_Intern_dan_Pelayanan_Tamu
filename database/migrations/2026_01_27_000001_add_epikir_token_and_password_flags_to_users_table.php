<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'epikir_letter_token')) {
                $table->string('epikir_letter_token', 120)->nullable()->after('internship_location_id');
            }

            if (!Schema::hasColumn('users', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false)->after('password');
                $table->index('must_change_password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'epikir_letter_token')) {
                $table->dropColumn('epikir_letter_token');
            }

            if (Schema::hasColumn('users', 'must_change_password')) {
                $table->dropIndex(['must_change_password']);
                $table->dropColumn('must_change_password');
            }
        });
    }
};
