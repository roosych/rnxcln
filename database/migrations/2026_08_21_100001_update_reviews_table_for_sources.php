<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Renames the original single-source columns to the shared multi-source
     * vocabulary (name -> author_name, image -> author_avatar, text ->
     * content, is_active -> published) instead of adding parallel columns,
     * then adds the fields needed to store reviews from any source.
     */
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->renameColumn('name', 'author_name');
            $table->renameColumn('image', 'author_avatar');
            $table->renameColumn('text', 'content');
            $table->renameColumn('is_active', 'published');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->foreignId('review_source_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('external_id')->nullable()->after('review_source_id');
            $table->unsignedTinyInteger('rating')->nullable()->after('author_avatar');
            $table->string('title')->nullable()->after('rating');
            $table->date('review_date')->nullable()->after('content');
            $table->text('reply')->nullable()->after('review_date');
            $table->timestamp('reply_date')->nullable()->after('reply');
            $table->string('source_url')->nullable()->after('reply_date');
            $table->boolean('featured')->default(false)->after('published');
            $table->boolean('verified')->default(false)->after('featured');
            $table->json('metadata')->nullable()->after('verified');

            $table->unique(['review_source_id', 'external_id']);
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropUnique(['review_source_id', 'external_id']);
            $table->dropConstrainedForeignId('review_source_id');
            $table->dropColumn([
                'external_id', 'rating', 'title', 'review_date', 'reply',
                'reply_date', 'source_url', 'featured', 'verified', 'metadata',
            ]);
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->renameColumn('author_name', 'name');
            $table->renameColumn('author_avatar', 'image');
            $table->renameColumn('content', 'text');
            $table->renameColumn('published', 'is_active');
        });
    }
};
