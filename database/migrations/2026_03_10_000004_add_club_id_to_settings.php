<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            // NULL = platform defaults (super_admin controlled)
            // non-null = club-specific overrides
            $table->foreignId('club_id')->nullable()->after('id')->constrained()->cascadeOnDelete();

            // Drop old unique on key alone; add composite unique
            $table->dropUnique(['key']);
            $table->unique(['club_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['club_id', 'key']);
            $table->dropForeign(['club_id']);
            $table->dropColumn('club_id');
            $table->unique('key');
        });
    }
};
