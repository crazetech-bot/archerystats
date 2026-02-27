<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->string('invitable_type'); // 'archer' or 'coach'
            $table->unsignedBigInteger('invitable_id');
            $table->string('token')->unique();
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamp('invited_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['invitable_type', 'invitable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_invitations');
    }
};
