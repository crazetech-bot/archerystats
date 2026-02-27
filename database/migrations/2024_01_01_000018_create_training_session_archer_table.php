<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_session_archer', function (Blueprint $table) {
            $table->foreignId('training_session_id')->constrained('training_sessions')->cascadeOnDelete();
            $table->foreignId('archer_id')->constrained()->cascadeOnDelete();
            $table->boolean('attended')->default(true);

            $table->primary(['training_session_id', 'archer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_session_archer');
    }
};
