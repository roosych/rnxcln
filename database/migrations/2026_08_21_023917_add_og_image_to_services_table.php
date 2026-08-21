<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Separate from `image` (the content photo, shown via
            // object-fit:cover on the site itself, any aspect ratio is
            // fine) — social previews don't crop as gracefully, so this
            // lets an admin upload something closer to the 1200x630 OG
            // convention without touching the content photo.
            $table->string('og_image')->nullable()->after('after_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('og_image');
        });
    }
};
