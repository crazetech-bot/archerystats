<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arrows', function (Blueprint $table) {
            $table->id();

            // Grafts onto the EXISTING `ends` table (score_id-based, arrow_values JSON).
            // The JSON column stays the fast path for plain value entry; this table is the
            // optional per-arrow layer that unlocks coordinate analytics.
            $table->foreignId('end_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('arrow_number'); // position within the end (1..n)

            // Derived value. The X still scores 10; a miss scores 0.
            $table->unsignedTinyInteger('score')->default(0);
            $table->boolean('is_x')->default(false);
            $table->boolean('is_miss')->default(false);

            // THE MOAT: impact position in millimetres from dead centre.
            // +x = right, +y = up. NULL for a miss / when only a value was entered.
            // Group size, barycentre, heatmaps, horizontal vs vertical stringing are all
            // computed from these two columns — the JSON arrow_values column can't do that.
            $table->decimal('x_mm', 6, 2)->nullable();
            $table->decimal('y_mm', 6, 2)->nullable();

            $table->timestamps();

            $table->index('end_id');
            $table->unique(['end_id', 'arrow_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arrows');
    }
};
