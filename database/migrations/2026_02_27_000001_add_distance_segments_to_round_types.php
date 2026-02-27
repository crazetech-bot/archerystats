<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('round_types', function (Blueprint $table) {
            $table->json('distance_segments')->nullable()->after('target_face_cm');
        });
    }

    public function down(): void
    {
        Schema::table('round_types', function (Blueprint $table) {
            $table->dropColumn('distance_segments');
        });
    }
};
