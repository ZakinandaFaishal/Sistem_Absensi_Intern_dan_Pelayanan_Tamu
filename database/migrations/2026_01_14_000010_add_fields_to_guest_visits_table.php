<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_visits', function (Blueprint $table) {
            $table->string('gender', 1)->nullable()->after('name');
            $table->string('email', 150)->nullable()->after('gender');
            $table->string('education', 30)->nullable()->after('email');
            $table->string('job', 120)->nullable()->after('phone');
            $table->string('jabatan', 120)->nullable()->after('job');
            $table->string('service_type', 20)->nullable()->after('jabatan');
            $table->string('purpose_detail', 500)->nullable()->after('service_type');
        });
    }

    public function down(): void
    {
        Schema::table('guest_visits', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'email',
                'education',
                'job',
                'jabatan',
                'service_type',
                'purpose_detail',
            ]);
        });
    }
};
