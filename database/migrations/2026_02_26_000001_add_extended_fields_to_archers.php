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
            $table->string('mareos_id')->nullable()->after('ref_no');
            $table->string('wareos_id')->nullable()->after('mareos_id');
            $table->string('division')->nullable()->after('wareos_id');
            $table->boolean('para_archery')->default(false)->after('classification');
            $table->string('state_team')->nullable()->after('team');
            $table->string('national_team')->default('No')->nullable()->after('state_team');
            $table->string('nric', 14)->nullable()->after('date_of_birth');
            $table->string('place_of_birth')->nullable()->after('nric');
            $table->string('next_of_kin_name')->nullable()->after('notes');
            $table->string('next_of_kin_relationship')->nullable()->after('next_of_kin_name');
            $table->string('next_of_kin_email')->nullable()->after('next_of_kin_relationship');
            $table->string('next_of_kin_phone')->nullable()->after('next_of_kin_email');
            $table->string('school')->nullable()->after('next_of_kin_phone');
            $table->text('school_address')->nullable()->after('school');
            $table->string('school_postcode', 10)->nullable()->after('school_address');
            $table->string('school_state')->nullable()->after('school_postcode');
        });

        // Copy existing 'team' value into 'state_team' for all existing archers
        DB::table('archers')->whereNotNull('team')->update([
            'state_team' => DB::raw('team'),
        ]);
    }

    public function down(): void
    {
        Schema::table('archers', function (Blueprint $table) {
            $table->dropColumn([
                'mareos_id', 'wareos_id', 'division', 'para_archery',
                'state_team', 'national_team', 'nric', 'place_of_birth',
                'next_of_kin_name', 'next_of_kin_relationship', 'next_of_kin_email', 'next_of_kin_phone',
                'school', 'school_address', 'school_postcode', 'school_state',
            ]);
        });
    }
};
