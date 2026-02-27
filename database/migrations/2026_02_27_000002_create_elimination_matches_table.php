<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('elimination_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('archer_a_id')->constrained('archers')->cascadeOnDelete();
            $table->foreignId('archer_b_id')->constrained('archers')->cascadeOnDelete();
            $table->string('category');             // outdoor, indoor, mssm
            $table->date('date');
            $table->string('location')->nullable();
            $table->string('competition_name')->nullable();
            $table->string('status')->default('in_progress'); // in_progress | completed
            $table->foreignId('winner_id')->nullable()->constrained('archers')->nullOnDelete();
            $table->boolean('shoot_off')->default(false);
            $table->foreignId('shoot_off_winner_id')->nullable()->constrained('archers')->nullOnDelete();
            // {"a":[["X","9","8"],["10","7","M"],...], "b":[...]}
            $table->json('arrow_values')->nullable();
            $table->string('shoot_off_a')->nullable();
            $table->string('shoot_off_b')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('elimination_matches');
    }
};
