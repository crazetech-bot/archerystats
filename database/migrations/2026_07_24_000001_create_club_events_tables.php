<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            // Set when a coach creates it — audience is scoped to their assigned archers.
            $table->foreignId('coach_id')->nullable()->constrained('coaches')->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('location', 200)->nullable();
            $table->string('event_type', 20)->default('other'); // training|competition|meeting|social|other
            $table->dateTime('starts_at');
            $table->dateTime('ends_at')->nullable();
            $table->string('audience', 20)->default('all'); // all|archers|coaches|my_archers
            $table->boolean('pinned')->default(false);
            $table->boolean('send_email')->default(false);
            $table->timestamp('emailed_at')->nullable();
            $table->unsignedSmallInteger('email_count')->default(0);
            $table->timestamps();

            $table->index(['club_id', 'starts_at']);
        });

        Schema::create('club_event_rsvps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('response', 12); // going|maybe|not_going
            $table->timestamp('responded_at')->useCurrent();

            $table->unique(['club_event_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_event_rsvps');
        Schema::dropIfExists('club_events');
    }
};
