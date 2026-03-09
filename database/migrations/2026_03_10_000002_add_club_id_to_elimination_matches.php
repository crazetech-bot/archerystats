<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elimination_matches', function (Blueprint $table) {
            $table->foreignId('club_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        // Backfill: derive club from archer_a_id
        DB::statement('
            UPDATE elimination_matches em
            JOIN archers a ON a.id = em.archer_a_id
            SET em.club_id = a.club_id
            WHERE em.club_id IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('elimination_matches', function (Blueprint $table) {
            $table->dropForeign(['club_id']);
            $table->dropColumn('club_id');
        });
    }
};
