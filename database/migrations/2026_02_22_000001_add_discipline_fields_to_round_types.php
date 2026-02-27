<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('round_types', function (Blueprint $table) {
            $table->string('discipline', 50)->nullable()->after('category');
            $table->integer('target_face_cm')->nullable()->after('distance_meters');
            $table->string('scoring_system', 20)->default('standard')->after('target_face_cm');
        });

        // Back-fill existing rows
        DB::table('round_types')->update(['scoring_system' => 'standard']);
    }

    public function down(): void
    {
        Schema::table('round_types', function (Blueprint $table) {
            $table->dropColumn(['discipline', 'target_face_cm', 'scoring_system']);
        });
    }
};
