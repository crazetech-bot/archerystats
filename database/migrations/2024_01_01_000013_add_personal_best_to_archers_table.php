<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archers', function (Blueprint $table) {
            // Personal Best — Unofficial (Training)
            $table->unsignedSmallInteger('pb_unofficial_36_score')->nullable()->after('actual_poundage');
            $table->date('pb_unofficial_36_date')->nullable()->after('pb_unofficial_36_score');
            $table->unsignedSmallInteger('pb_unofficial_72_score')->nullable()->after('pb_unofficial_36_date');
            $table->date('pb_unofficial_72_date')->nullable()->after('pb_unofficial_72_score');

            // Personal Best — Official
            $table->unsignedSmallInteger('pb_official_36_score')->nullable()->after('pb_unofficial_72_date');
            $table->date('pb_official_36_date')->nullable()->after('pb_official_36_score');
            $table->string('pb_official_36_tournament')->nullable()->after('pb_official_36_date');
            $table->unsignedSmallInteger('pb_official_72_score')->nullable()->after('pb_official_36_tournament');
            $table->date('pb_official_72_date')->nullable()->after('pb_official_72_score');
            $table->string('pb_official_72_tournament')->nullable()->after('pb_official_72_date');
        });
    }

    public function down(): void
    {
        Schema::table('archers', function (Blueprint $table) {
            $table->dropColumn([
                'pb_unofficial_36_score', 'pb_unofficial_36_date',
                'pb_unofficial_72_score', 'pb_unofficial_72_date',
                'pb_official_36_score',   'pb_official_36_date',   'pb_official_36_tournament',
                'pb_official_72_score',   'pb_official_72_date',   'pb_official_72_tournament',
            ]);
        });
    }
};
