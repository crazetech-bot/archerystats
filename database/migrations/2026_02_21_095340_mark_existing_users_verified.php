<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE users SET email_verified_at = NOW() WHERE email_verified_at IS NULL");
    }

    public function down(): void
    {
        // No rollback — pre-existing users should remain verified
    }
};
