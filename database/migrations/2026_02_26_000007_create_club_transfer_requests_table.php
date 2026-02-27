<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_transfer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('archer_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('from_club_id')->nullable(); // null = unaffiliated
            $table->unsignedBigInteger('to_club_id');
            $table->foreign('to_club_id')->references('id')->on('clubs')->cascadeOnDelete();
            $table->string('token', 64)->unique();
            $table->string('status', 20)->default('pending'); // pending, approved, declined, cancelled
            $table->timestamp('expires_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_transfer_requests');
    }
};
