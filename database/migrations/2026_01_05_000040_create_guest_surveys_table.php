<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('guest_visits')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->string('comment', 500)->nullable();
            $table->timestamp('submitted_at');
            $table->timestamps();

            $table->unique('visit_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_surveys');
    }
};
