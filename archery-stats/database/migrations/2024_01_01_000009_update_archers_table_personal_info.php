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
            $table->string('ref_no', 20)->nullable()->unique()->after('id');
            $table->string('team')->nullable()->after('club_id');
            $table->string('state')->nullable()->after('team');
            $table->string('country')->default('Malaysia')->after('state');
            $table->enum('gender', ['male', 'female'])->nullable()->after('date_of_birth');
            $table->renameColumn('address', 'address_line');
            $table->string('postcode', 10)->nullable()->after('address_line');
            $table->string('address_state')->nullable()->after('postcode');
            $table->json('divisions')->nullable()->after('bow_style');
        });

        // Make bow_style nullable without doctrine/dbal
        DB::statement("ALTER TABLE archers MODIFY bow_style ENUM('recurve','compound','barebow','longbow','traditional') NULL DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE archers MODIFY bow_style ENUM('recurve','compound','barebow','longbow','traditional') NOT NULL DEFAULT 'recurve'");

        Schema::table('archers', function (Blueprint $table) {
            $table->dropColumn(['ref_no', 'team', 'state', 'country', 'gender', 'postcode', 'address_state', 'divisions']);
            $table->renameColumn('address_line', 'address');
        });
    }
};
