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
        Schema::create('football_seasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sport_id')->index()->default(1);
            $table->unsignedBigInteger('season_id')->index();
            $table->unsignedBigInteger('league_id')->index();
            $table->unsignedBigInteger('tie_breaker_rule_id')->nullable();
            $table->string('name');
            $table->boolean('finished')->default(false);
            $table->boolean('pending')->default(false);
            $table->boolean('is_current')->default(false);
            $table->date('starting_at');
            $table->date('ending_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_seasons');
    }
};
