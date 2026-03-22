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
        Schema::create('join_crick_contests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('contest_id')->nullable();
            $table->unsignedBigInteger('created_team_id')->nullable();
            $table->text('teams')->nullable();
            $table->text('team_count')->nullable();
            $table->integer('ranks')->default(0);
            $table->float('points', 10, 2)->default(0.00);
            // $table->integer('mega_pass')->default(0);
            $table->float('prize_amount', 10, 2)->default(0.00);
            $table->float('winning_amount', 10, 2)->default(0.00);
            $table->float('entryfee_bonus', 10, 2)->default(0.00);
            $table->float('entryfee_deposit', 10, 2)->default(0.00);
            $table->float('entryfee_winning', 10, 2)->default(0.00);
            // $table->float('entryfee_extracash', 10, 2)->default(0.00);
            $table->boolean('cancel_contest')->default(false);
            // $table->boolean('affiliated_user')->default(false);
            // $table->integer('premium_team')->default(0);
            // $table->unsignedBigInteger('expert_user_id')->nullable();
            // $table->unsignedBigInteger('expert_team_id')->nullable();
            $table->string('user_name', 255)->nullable();
            $table->string('team_name', 255)->nullable();
            $table->boolean('is_prize_distributed')->default(false);
            $table->boolean('is_inv_cal')->default(false)->comment('weekly investment calculation');
            $table->boolean('is_inv_cal_mon')->default(false);
            $table->timestamps();

            $table->foreign('match_id')->references('fixture_id')->on('fixtures');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('contest_id')->references('id')->on('contests');
            $table->foreign('created_team_id')->references('id')->on('user_teams');
            $table->unique(['match_id','contest_id'], 'unique_match_contest');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('join_crick_contests');
    }
};
