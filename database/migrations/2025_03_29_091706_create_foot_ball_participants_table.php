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
        Schema::create('football_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id')->index(); // Foreign key reference to FootBallMatch
            $table->unsignedBigInteger('team_id')->index(); // Foreign key reference to FootBallMatch
            $table->unsignedBigInteger('sport_id')->index()->default(1);
            $table->unsignedBigInteger('country_id')->index();
            $table->unsignedBigInteger('venue_id')->index();
            $table->string('gender');
            $table->string('name');
            $table->string('short_code')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('founded')->nullable();
            $table->string('type')->nullable();
            $table->dateTime('last_played_at')->nullable();
            $table->string('location')->nullable();
            $table->integer('position')->nullable();
            $table->timestamps();

            // $table->foreign('match_id')->references('id')->on('foot_ball_matches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_participants');
    }
};
