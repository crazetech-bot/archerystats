<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('round_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "WA 70m", "WA 18m Indoor", "Frostbite"
            $table->string('category')->nullable(); // outdoor, indoor, field, 3D
            $table->integer('distance_meters')->nullable();
            $table->integer('num_ends'); // number of ends
            $table->integer('arrows_per_end'); // arrows per end
            $table->integer('max_score_per_arrow')->default(10); // 10 or 5 for some rounds
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('round_types');
    }
};
