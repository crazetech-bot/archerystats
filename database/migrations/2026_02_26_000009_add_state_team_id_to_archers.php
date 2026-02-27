<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archers', function (Blueprint $table) {
            $table->foreignId('state_team_id')
                  ->nullable()
                  ->after('state_team')
                  ->constrained('state_teams')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('archers', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\StateTeam::class);
            $table->dropColumn('state_team_id');
        });
    }
};
