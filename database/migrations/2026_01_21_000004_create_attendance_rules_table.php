<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dinas_id')->constrained('dinas')->cascadeOnDelete();

            $table->decimal('office_lat', 10, 7)->nullable();
            $table->decimal('office_lng', 10, 7)->nullable();
            $table->unsignedSmallInteger('radius_m')->default(50);
            $table->unsignedSmallInteger('max_accuracy_m')->default(50);

            $table->string('checkin_start')->default('08:00');
            $table->string('checkin_end')->default('12:00');
            $table->string('checkout_start')->default('13:00');
            $table->string('checkout_end')->default('16:30');

            $table->timestamps();

            $table->unique('dinas_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_rules');
    }
};
