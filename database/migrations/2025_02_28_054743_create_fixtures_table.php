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
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fixture_id')->unique()->comment('External API fixture ID');
            $table->bigInteger('league_id')->index();
            $table->bigInteger('season_id')->index();
            $table->bigInteger('stage_id')->nullable()->index();
            $table->string('round')->nullable();
            $table->bigInteger('localteam_id')->index();
            $table->bigInteger('visitorteam_id')->index();
            $table->timestamp('starting_at')->nullable()->index();
            $table->string('type')->nullable()->index();
            $table->boolean('live')->default(false);
            $table->string('status')->nullable()->index();
            $table->string('last_period')->nullable();
            $table->text('note')->nullable();
            $table->bigInteger('venue_id')->nullable()->index();
            $table->bigInteger('toss_won_team_id')->nullable();
            $table->bigInteger('winner_team_id')->nullable()->index();
            $table->string('draw_noresult')->nullable();
            $table->bigInteger('first_umpire_id')->nullable();
            $table->bigInteger('second_umpire_id')->nullable();
            $table->bigInteger('tv_umpire_id')->nullable();
            $table->bigInteger('referee_id')->nullable();
            $table->bigInteger('man_of_match_id')->nullable();
            $table->bigInteger('man_of_series_id')->nullable();
            $table->decimal('total_overs_played', 5, 1)->nullable();
            $table->string('elected')->nullable();
            $table->boolean('super_over')->default(false);
            $table->boolean('follow_on')->default(false);

            // DL data as JSON columns
            $table->json('localteam_dl_data')->nullable();
            $table->json('visitorteam_dl_data')->nullable();

            $table->decimal('rpc_overs', 5, 1)->nullable();
            $table->integer('rpc_target')->nullable();
            $table->json('weather_report')->nullable();

            // We'll store basic team info for quick access
            $table->string('localteam_name')->nullable();
            $table->string('localteam_code')->nullable();
            $table->string('localteam_image_path')->nullable();

            $table->string('visitorteam_name')->nullable();
            $table->string('visitorteam_code')->nullable();
            $table->string('visitorteam_image_path')->nullable();

            $table->timestamps();

            // Add indexes for most frequently queried fields
            $table->index(['status', 'starting_at']);
            $table->index(['league_id', 'season_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixtures');
    }
};
