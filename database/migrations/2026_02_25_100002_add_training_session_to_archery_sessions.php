<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archery_sessions', function (Blueprint $table) {
            $table->foreignId('training_session_id')
                  ->nullable()
                  ->constrained('training_sessions')
                  ->nullOnDelete()
                  ->after('notes');
            $table->boolean('assigned_by_coach')->default(false)->after('training_session_id');
        });
    }

    public function down(): void
    {
        Schema::table('archery_sessions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('training_session_id');
            $table->dropColumn('assigned_by_coach');
        });
    }
};
