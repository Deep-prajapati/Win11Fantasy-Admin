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
        Schema::create('default_contests', function (Blueprint $table) {
            $table->id();
            $table->integer('contest_type')->nullable();
            $table->integer('mrp')->default(0);
            $table->integer('entry_fees')->nullable();
            $table->string('total_spots')->nullable();
            $table->integer('first_prize')->nullable();
            $table->float('prize_percentage')->nullable();
            $table->integer('winner_percentage')->default(50);
            $table->string('cancellation')->nullable();
            $table->integer('total_winning_prize')->nullable();
            $table->unsignedInteger('match_id')->nullable();
            $table->boolean('is_free')->default(false);
            $table->boolean('is3x')->default(false);
            $table->integer('extra_cash')->default(0);
            $table->boolean('bonus_contest')->nullable();
            $table->integer('usable_bonus')->default(10);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_contests');
    }
};
