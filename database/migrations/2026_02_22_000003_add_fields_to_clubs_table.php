<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('website')->nullable()->after('contact_phone');
            $table->string('address')->nullable()->after('website');
            $table->string('state')->nullable()->after('address');
            $table->smallInteger('founded_year')->unsigned()->nullable()->after('state');
            $table->string('registration_number')->nullable()->after('founded_year');
        });
    }

    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn(['description', 'website', 'address', 'state', 'founded_year', 'registration_number']);
        });
    }
};
