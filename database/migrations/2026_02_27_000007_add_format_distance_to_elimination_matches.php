<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('elimination_matches', function (Blueprint $table) {
            $table->string('format')->default('set_point')->after('category');
            $table->tinyInteger('distance_m')->unsigned()->nullable()->after('format');
        });
    }

    public function down(): void
    {
        Schema::table('elimination_matches', function (Blueprint $table) {
            $table->dropColumn(['format', 'distance_m']);
        });
    }
};
