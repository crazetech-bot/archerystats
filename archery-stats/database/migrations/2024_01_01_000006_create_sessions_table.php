<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archery_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('archer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('round_type_id')->constrained();
            $table->date('date');
            $table->string('location')->nullable();
            $table->enum('weather', ['sunny', 'cloudy', 'windy', 'rain', 'indoor', 'other'])
                  ->nullable();
            $table->boolean('is_competition')->default(false);
            $table->string('competition_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archery_sessions');
    }
};
