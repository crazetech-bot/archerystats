<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('round_types', function (Blueprint $table) {
            $table->boolean('is_custom')->default(false)->after('active');
        });
    }

    public function down(): void
    {
        Schema::table('round_types', function (Blueprint $table) {
            $table->dropColumn('is_custom');
        });
    }
};
