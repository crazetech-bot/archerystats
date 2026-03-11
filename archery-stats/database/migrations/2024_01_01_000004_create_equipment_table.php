<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('archer_id')->constrained()->cascadeOnDelete();
            $table->string('bow_brand')->nullable();
            $table->string('bow_model')->nullable();
            $table->enum('bow_type', ['recurve', 'compound', 'barebow', 'longbow', 'traditional'])
                  ->default('recurve');
            $table->decimal('draw_weight', 5, 1)->nullable(); // pounds
            $table->decimal('draw_length', 5, 1)->nullable(); // inches
            $table->string('arrow_brand')->nullable();
            $table->string('arrow_model')->nullable();
            $table->integer('arrow_spine')->nullable();
            $table->decimal('arrow_length', 5, 1)->nullable(); // inches
            $table->decimal('arrow_weight', 6, 1)->nullable(); // grains
            $table->string('sight')->nullable();
            $table->string('stabilizer')->nullable();
            $table->string('arrow_rest')->nullable();
            $table->string('release_aid')->nullable(); // for compound
            $table->text('notes')->nullable();
            $table->boolean('current')->default(true); // current setup
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
