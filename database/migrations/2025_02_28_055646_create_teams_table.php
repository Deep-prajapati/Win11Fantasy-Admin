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
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('team_id')->unique()->comment('External API team ID');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('image_path')->nullable();
            $table->bigInteger('country_id')->nullable();
            $table->boolean('national_team')->default(false);
            $table->timestamps();

            // Add indexes
            $table->index('name');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
