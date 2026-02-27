<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archers', function (Blueprint $table) {
            $table->string('status')->default('active')->after('active');
            $table->date('injury_date')->nullable()->after('status');
            $table->string('injury_type')->nullable()->after('injury_date');
            $table->date('injury_return_date')->nullable()->after('injury_type');
        });

        // Migrate existing inactive archers
        DB::table('archers')->where('active', false)->update(['status' => 'no_longer_active']);
    }

    public function down(): void
    {
        Schema::table('archers', function (Blueprint $table) {
            $table->dropColumn(['status', 'injury_date', 'injury_type', 'injury_return_date']);
        });
    }
};
