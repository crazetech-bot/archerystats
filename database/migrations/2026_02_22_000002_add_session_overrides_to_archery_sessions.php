<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archery_sessions', function (Blueprint $table) {
            $table->integer('distance_meters')->nullable()->after('round_type_id');
            $table->integer('target_face_cm')->nullable()->after('distance_meters');
        });
    }

    public function down(): void
    {
        Schema::table('archery_sessions', function (Blueprint $table) {
            $table->dropColumn(['distance_meters', 'target_face_cm']);
        });
    }
};
