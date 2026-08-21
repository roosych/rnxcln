<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('source', 32)->index();
            $table->string('name', 120);
            $table->string('phone', 40);
            $table->string('service', 120)->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('preferred_date', 40)->nullable();
            $table->text('message')->nullable();
            $table->enum('status', ['new', 'contacted', 'booked', 'closed'])->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
