<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing FK constraints before altering columns
        Schema::table('elimination_matches', function (Blueprint $table) {
            $table->dropForeign(['archer_a_id']);
            $table->dropForeign(['archer_b_id']);
        });

        // Make archer IDs nullable (raw SQL — safest across MySQL versions)
        DB::statement('ALTER TABLE elimination_matches MODIFY archer_a_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE elimination_matches MODIFY archer_b_id BIGINT UNSIGNED NULL');

        Schema::table('elimination_matches', function (Blueprint $table) {
            // Re-add FK constraints with nullOnDelete
            $table->foreign('archer_a_id')->references('id')->on('archers')->nullOnDelete();
            $table->foreign('archer_b_id')->references('id')->on('archers')->nullOnDelete();
            // Manual name fallback when archer is not a registered user
            $table->string('archer_a_name')->nullable()->after('archer_a_id');
            $table->string('archer_b_name')->nullable()->after('archer_b_id');
        });
    }

    public function down(): void
    {
        Schema::table('elimination_matches', function (Blueprint $table) {
            $table->dropForeign(['archer_a_id']);
            $table->dropForeign(['archer_b_id']);
            $table->dropColumn(['archer_a_name', 'archer_b_name']);
        });

        DB::statement('ALTER TABLE elimination_matches MODIFY archer_a_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE elimination_matches MODIFY archer_b_id BIGINT UNSIGNED NOT NULL');

        Schema::table('elimination_matches', function (Blueprint $table) {
            $table->foreign('archer_a_id')->references('id')->on('archers')->cascadeOnDelete();
            $table->foreign('archer_b_id')->references('id')->on('archers')->cascadeOnDelete();
        });
    }
};
