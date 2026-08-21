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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            // No folder/category table — 'section' is a plain string, fixed to
            // whichever key App\Models\Service::DEFAULT_FOLDERS defines (currently
            // 'core' / 'home-office'); the section only decides which page listing
            // a service appears in, not this service's own page.
            $table->string('section', 20)->nullable();
            $table->string('title', 160);
            $table->string('slug', 160)->unique();
            $table->string('tagline', 200)->nullable();
            $table->text('text')->nullable();
            $table->string('image')->nullable();
            $table->string('alt', 200)->nullable();
            $table->json('items')->nullable();
            // page/contact/custom — see App\Models\Service::url().
            $table->string('link_type', 20)->default('page');
            $table->string('link_url')->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->string('before_image')->nullable();
            $table->string('after_image')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
