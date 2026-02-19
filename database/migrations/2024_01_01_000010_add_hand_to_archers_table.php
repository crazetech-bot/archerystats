<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archers', function (Blueprint $table) {
            $table->enum('hand', ['right', 'left'])->nullable()->after('team');
        });
    }

    public function down(): void
    {
        Schema::table('archers', function (Blueprint $table) {
            $table->dropColumn('hand');
        });
    }
};
