<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            // Grafts onto the EXISTING coach-owned training_sessions table. Kept separate
            // from the training_session_archer pivot (which marks who is *assigned* a
            // scoring round) so attendance status never entangles with scoring assignment.
            $table->foreignId('training_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('archer_id')->constrained()->cascadeOnDelete();

            $table->enum('status', ['present', 'late', 'absent', 'excused'])->default('present');

            $table->timestamps();

            // One attendance row per archer per session — re-saving updates, never duplicates.
            $table->unique(['training_session_id', 'archer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
