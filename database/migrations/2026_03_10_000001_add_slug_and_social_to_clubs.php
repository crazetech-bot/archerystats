<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Club;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('tagline')->nullable()->after('description');
            $table->string('facebook_url')->nullable()->after('website');
            $table->string('instagram_url')->nullable()->after('facebook_url');
            $table->string('whatsapp_number')->nullable()->after('instagram_url');
        });

        // Backfill slugs for existing clubs
        Club::withoutGlobalScopes()->each(function (Club $club) {
            $base = Str::slug($club->name);
            $slug = $base;
            $i = 2;
            while (Club::withoutGlobalScopes()->where('slug', $slug)->where('id', '!=', $club->id)->exists()) {
                $slug = $base . '-' . $i++;
            }
            $club->updateQuietly(['slug' => $slug]);
        });

        // Make slug non-nullable after backfill
        Schema::table('clubs', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('clubs', function (Blueprint $table) {
            $table->dropColumn(['slug', 'tagline', 'facebook_url', 'instagram_url', 'whatsapp_number']);
        });
    }
};
