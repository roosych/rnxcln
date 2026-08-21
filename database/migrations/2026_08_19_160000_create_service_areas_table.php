<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
| Service areas (contact form ZIP dropdown) — one row per ZIP with its own
| active flag, sorted alphabetically by area name (no sort_order: this can
| realistically grow into the hundreds, and search + pagination don't mix
| well with drag-to-reorder).
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_areas', function (Blueprint $table) {
            $table->id();
            $table->string('zip', 20)->unique();
            $table->string('area', 160);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seeded from config, not Setting::get() — this runs at `migrate` time,
        // before `content:import-config` has put anything into the settings
        // table, so a Setting-backed lookup here would silently seed nothing.
        $now = now();

        foreach (config('site.service_zips', []) as $zip => $area) {
            DB::table('service_areas')->insert([
                'zip' => $zip,
                'area' => $area,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('service_areas');
    }
};
