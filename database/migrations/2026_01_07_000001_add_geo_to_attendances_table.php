<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('lat', 10, 7)->nullable()->after('location_id');
            $table->decimal('lng', 10, 7)->nullable()->after('lat');
            $table->unsignedInteger('accuracy_m')->nullable()->after('lng');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['lat', 'lng', 'accuracy_m']);
        });
    }
};
