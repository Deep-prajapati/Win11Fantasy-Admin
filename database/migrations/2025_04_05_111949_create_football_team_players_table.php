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
        Schema::create('football_team_players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('season_id')->default(0);
            $table->unsignedBigInteger('transfer_id')->default(0);
            $table->unsignedBigInteger('player_id')->default(0);
            $table->unsignedBigInteger('team_id')->default(0);
            $table->unsignedBigInteger('position_id')->default(0);
            $table->unsignedBigInteger('detailed_position_id')->default(0);
            $table->date('start')->default('1000-01-01');
            $table->date('end')->default('1000-01-01');
            $table->boolean('captain')->default(false);
            $table->unsignedInteger('jersey_number')->default(0);
            $table->string('position_name')->default('');
            $table->string('position_code')->default('');
            $table->string('position_developer_name')->default('');
            $table->string('position_model_type')->default('');
            $table->string('position_stat_group')->default('');
            $table->timestamps();

            $table->unique(['season_id', 'team_id', 'player_id'], 'season_team_player_unique');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_team_players');
    }
};
