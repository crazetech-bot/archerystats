<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('super_admin','club_admin','state_admin','coach','archer','guest') NOT NULL DEFAULT 'archer'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('super_admin','club_admin','coach','archer','guest') NOT NULL DEFAULT 'archer'");
    }
};
