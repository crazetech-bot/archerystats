<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_announcements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_user_id')->nullable()->constrained('users')->nullOnDelete();
            // Set when a coach posts — audience is scoped to their assigned archers.
            $table->foreignId('coach_id')->nullable()->constrained('coaches')->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('body');
            $table->string('audience', 20)->default('all'); // all | archers | coaches | my_archers
            $table->boolean('send_email')->default(false);
            $table->timestamp('emailed_at')->nullable();
            $table->unsignedSmallInteger('email_count')->default(0);
            $table->timestamps();

            $table->index(['club_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_announcements');
    }
};
