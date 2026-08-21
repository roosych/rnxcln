<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('provider', 40)->unique();
            $table->string('type', 20);
            $table->boolean('enabled')->default(false);
            $table->boolean('connected')->default(false);
            $table->json('config')->nullable();
            $table->text('credentials')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->string('sync_status', 20)->nullable();
            $table->text('sync_error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_sources');
    }
};
