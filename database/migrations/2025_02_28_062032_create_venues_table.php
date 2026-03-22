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
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('venue_id')->unique()->comment('External API venue ID');
            $table->bigInteger('country_id')->nullable()->index();
            $table->string('name')->index();
            $table->string('city')->nullable()->index();
            $table->string('image_path')->nullable();
            $table->integer('capacity')->nullable();
            $table->boolean('floodlight')->default(false);
            $table->timestamps();

            // Add combined indexes for common searches
            $table->index(['country_id', 'city']);
            $table->index(['floodlight', 'capacity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
