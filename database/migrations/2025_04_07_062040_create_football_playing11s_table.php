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
        Schema::create('football_playing11s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sport_id')->default(1)->index();
            $table->unsignedBigInteger('match_id')->index();
            $table->unsignedBigInteger('player_id')->index();
            $table->unsignedBigInteger('team_id')->index();
            $table->unsignedBigInteger('position_id')->default(0);
            $table->unsignedBigInteger('detailed_position_id')->default(0);

            $table->string('formation_field')->default('');
            $table->unsignedBigInteger('type_id')->default(0);
            $table->integer('jersey_number')->default(0);
            $table->integer('formation_position')->default(0);

            $table->string('player_name')->default('');

            $table->timestamps();
            $table->unique(['player_id','team_id','match_id'],'player_team_match_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_playing11s');
    }
};
