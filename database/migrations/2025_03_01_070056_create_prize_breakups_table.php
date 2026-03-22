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
        Schema::create('prize_breakups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('default_contest_id');
            $table->unsignedInteger('contest_type_id')->nullable();
            $table->integer('rank_from')->nullable();
            $table->integer('rank_upto')->nullable();
            $table->float('prize_amount', 10, 1)->default(0.0);
            $table->timestamps();

            $table->foreign('default_contest_id')->references('id')->on('default_contests');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_breakups');
    }
};
