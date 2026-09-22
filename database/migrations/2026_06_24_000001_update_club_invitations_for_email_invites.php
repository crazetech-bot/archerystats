<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('club_invitations', function (Blueprint $table) {
            // Email-only invitations (no existing archer/coach profile yet)
            $table->unsignedBigInteger('invitable_id')->nullable()->change();
            $table->string('email')->nullable()->index()->after('invitable_id');
        });
    }

    public function down(): void
    {
        Schema::table('club_invitations', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropColumn('email');
            $table->unsignedBigInteger('invitable_id')->nullable(false)->change();
        });
    }
};
