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
        Schema::create('battings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fixture_id')->index();
            $table->bigInteger('team_id')->index();
            $table->bigInteger('player_id')->index();
            $table->string('scoreboard');
            $table->integer('sort');
            $table->boolean('active')->default(true);
            $table->bigInteger('wicket_id')->nullable();
            $table->integer('ball');
            $table->bigInteger('score_id');
            $table->integer('score');
            $table->integer('four_x');
            $table->integer('six_x');
            $table->bigInteger('catch_stump_player_id')->nullable();
            $table->bigInteger('runout_by_id')->nullable();
            $table->bigInteger('batsmanout_id')->nullable();
            $table->bigInteger('bowling_player_id')->nullable();
            $table->integer('fow_score');
            $table->integer('fow_balls');
            $table->decimal('rate', 5, 2);
            $table->timestamps();
            $table->index(['fixture_id', 'team_id', 'player_id', 'scoreboard']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battings');
    }
};
