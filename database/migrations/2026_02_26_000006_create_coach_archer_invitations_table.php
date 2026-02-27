<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_archer_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coach_id')->constrained()->cascadeOnDelete();
            $table->foreignId('archer_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('status', 20)->default('pending'); // pending, accepted, declined
            $table->timestamp('expires_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['coach_id', 'archer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_archer_invitations');
    }
};
