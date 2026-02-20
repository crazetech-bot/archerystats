<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ref_no')->nullable()->unique();
            $table->date('date_of_birth')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('team', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('country', 100)->default('Malaysia');
            $table->text('address_line')->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('photo')->nullable();
            $table->boolean('active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coaches');
    }
};
