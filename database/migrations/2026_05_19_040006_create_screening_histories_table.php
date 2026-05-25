<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('screening_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->json('selected_symptoms');
            $table->float('cf_score_raw', 8, 4);
            $table->float('cf_score_percentage', 5, 2);
            $table->string('risk_level');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('screening_histories');
    }
};