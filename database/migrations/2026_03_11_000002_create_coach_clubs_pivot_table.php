<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_clubs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coach_id');
            $table->unsignedBigInteger('club_id');
            $table->boolean('primary_club')->default(false);
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['coach_id', 'club_id']);
            $table->foreign('coach_id')->references('id')->on('coaches')->onDelete('cascade');
            $table->foreign('club_id')->references('id')->on('clubs')->onDelete('cascade');
        });

        // Backfill from existing coaches.club_id
        DB::statement("
            INSERT INTO coach_clubs (coach_id, club_id, primary_club, joined_at, created_at, updated_at)
            SELECT id, club_id, 1, created_at, NOW(), NOW()
            FROM coaches
            WHERE club_id IS NOT NULL
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_clubs');
    }
};
