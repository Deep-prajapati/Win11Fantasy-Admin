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
        Schema::create('football_contests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('match_id')->nullable();
            $table->unsignedInteger('contest_type')->nullable();
            $table->unsignedInteger('total_winning_prize')->nullable();
            $table->integer('mrp')->default(0);
            $table->unsignedInteger('entry_fees')->nullable();
            $table->unsignedInteger('total_spots')->nullable();
            $table->unsignedInteger('filled_spot')->default(0);
            $table->float('first_prize', 10, 2)->default(0.00);
            $table->float('winner_percentage', 10, 2)->default(0.00);
            $table->integer('prize_percentage')->default(0);
            $table->string('cancellation')->nullable();
            $table->unsignedBigInteger('default_contest_id')->nullable();
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_cancelable')->default(false);
            $table->boolean('is_free')->default(false);
            $table->boolean('is3x')->default(false);
            $table->integer('extra_cash')->default(0);
            $table->boolean('is_cloned')->default(false);
            $table->boolean('is_full')->default(false);
            $table->integer('sort_by')->default(0);
            $table->integer('usable_bonus')->nullable();
            $table->boolean('bonus_contest')->default(false);
            $table->boolean('auto_create')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('default_contest_id')->references('id')->on('football_default_contests');
            $table->unique(['match_id', 'default_contest_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_contests');
    }
};
