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
        Schema::create('bowlings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fixture_id')->index();
            $table->bigInteger('team_id')->index();
            $table->bigInteger('player_id')->index();
            $table->string('scoreboard');
            $table->integer('overs');
            $table->integer('medians');
            $table->integer('runs');
            $table->integer('wickets');
            $table->integer('wide');
            $table->integer('noball');
            $table->decimal('rate', 5, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->index(['fixture_id', 'team_id', 'player_id', 'scoreboard']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bowlings');
    }
};
