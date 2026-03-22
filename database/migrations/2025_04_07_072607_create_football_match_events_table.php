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
        Schema::create('football_match_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id')->unique()->index();
            $table->unsignedBigInteger('match_id')->index();
            $table->unsignedBigInteger('period_id')->index();
            $table->unsignedBigInteger('participant_id')->index();
            $table->unsignedBigInteger('type_id')->index();
            $table->unsignedBigInteger('player_id')->index();
            $table->unsignedBigInteger('related_player_id')->nullable()->index();
            $table->string('player_name');
            $table->string('related_player_name')->nullable();
            $table->string('result')->nullable();
            $table->text('info')->nullable();
            $table->text('addition')->nullable();
            $table->unsignedInteger('minute');
            $table->unsignedInteger('extra_minute')->nullable();
            $table->boolean('injured')->default(false);
            $table->unsignedBigInteger('team_id')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_match_events');
    }
};
