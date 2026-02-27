<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archer_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('archer_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('achievement');
            $table->string('team')->nullable();
            $table->string('tournament')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archer_achievements');
    }
};
