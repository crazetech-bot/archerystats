<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('archers', function (Blueprint $table) {
            $table->string('arrow_type')->nullable()->after('notes');
            $table->string('arrow_size')->nullable()->after('arrow_type');
            $table->decimal('arrow_length', 5, 1)->nullable()->after('arrow_size');
            $table->string('limb_type')->nullable()->after('arrow_length');
            $table->decimal('limb_length', 5, 1)->nullable()->after('limb_type');
            $table->decimal('limb_poundage', 5, 1)->nullable()->after('limb_length');
            $table->decimal('actual_poundage', 5, 1)->nullable()->after('limb_poundage');
        });
    }

    public function down(): void
    {
        Schema::table('archers', function (Blueprint $table) {
            $table->dropColumn([
                'arrow_type', 'arrow_size', 'arrow_length',
                'limb_type', 'limb_length', 'limb_poundage', 'actual_poundage',
            ]);
        });
    }
};
