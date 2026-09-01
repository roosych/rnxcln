<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Blog categories are the existing service catalog, not a separate
     * taxonomy — a post is tagged with 1+ services, shown as pill labels
     * (see public/css/style.css .mil-category). The pivot's own
     * auto-increment id (not the FK columns) is what an admin's tick
     * order is preserved by — see App\Models\BlogPost::categories().
     */
    public function up(): void
    {
        Schema::create('blog_post_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->unique(['blog_post_id', 'service_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_post_service');
    }
};
