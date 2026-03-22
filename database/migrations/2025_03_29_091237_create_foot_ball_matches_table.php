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
        Schema::create('football_matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id')->index();
            $table->unsignedBigInteger('sport_id')->index()->default(1);
            $table->unsignedBigInteger('league_id')->index();
            $table->unsignedBigInteger('season_id')->index();
            $table->unsignedBigInteger('stage_id')->index();
            $table->unsignedBigInteger('round_id')->index();
            $table->unsignedBigInteger('venue_id');
            $table->string('name');
            $table->dateTime('starting_at');
            $table->integer('length')->default(90);
            $table->boolean('placeholder')->default(false);
            $table->boolean('has_odds')->default(false);
            $table->boolean('has_premium_odds')->default(false);
            $table->bigInteger('starting_at_timestamp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foot_ball_matches');
    }
};
