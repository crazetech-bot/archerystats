<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('archery_session_id')->constrained('archery_sessions')->cascadeOnDelete();
            $table->integer('total_score')->default(0);
            $table->integer('x_count')->default(0);   // X/10 hits (perfect)
            $table->integer('gold_count')->default(0); // 10 & X hits
            $table->integer('hit_count')->default(0);  // any hit
            $table->integer('miss_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
