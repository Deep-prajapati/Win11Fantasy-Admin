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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id')->unique()->comment('External API player ID');
            $table->bigInteger('country_id')->nullable()->index();
            $table->bigInteger('team_id')->nullable()->index();
            $table->bigInteger('season_id')->index();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('fullname')->index();
            $table->string('image_path')->nullable();
            $table->date('dateofbirth')->nullable();
            $table->enum('gender', ['m', 'f'])->nullable();
            $table->string('battingstyle')->nullable();
            $table->string('bowlingstyle')->nullable();
            $table->bigInteger('position_id')->nullable()->index();
            $table->string('position_name')->nullable();
            $table->timestamps();
            $table->unique(['player_id', 'team_id','season_id'], 'unique_player_team_season');

            // Add combined indexes for common searches
            $table->index(['position_id', 'country_id']);
            $table->index(['firstname', 'lastname']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
