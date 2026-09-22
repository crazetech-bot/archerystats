<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('club_announcements', function (Blueprint $table) {
            $table->boolean('pinned')->default(false)->after('audience');
        });

        Schema::create('club_announcement_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_announcement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->useCurrent();

            $table->unique(['club_announcement_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_announcement_reads');
        Schema::table('club_announcements', function (Blueprint $table) {
            $table->dropColumn('pinned');
        });
    }
};
