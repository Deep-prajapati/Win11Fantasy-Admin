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
        Schema::create('football_players_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id')->index();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('player_id')->index();
            $table->bigInteger('points')->default(0);
            $table->timestamps();
            $table->unique(['player_id','team_id','match_id'],'player_team_match_unique');
        });
    }
    

    public function down(): void
    {
        Schema::dropIfExists('football_players_points');
    }
};
