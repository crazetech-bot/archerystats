<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elimination_matches', function (Blueprint $table) {
            // 'a' or 'b' — set manually when shoot-off arrows are equal (nearest to center)
            $table->string('nearest_to_center')->nullable()->after('shoot_off_b');
        });
    }

    public function down(): void
    {
        Schema::table('elimination_matches', function (Blueprint $table) {
            $table->dropColumn('nearest_to_center');
        });
    }
};
