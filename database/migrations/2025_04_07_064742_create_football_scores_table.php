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
        Schema::create('football_scores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id')->index();
            $table->unsignedBigInteger('participant_id')->index();
            $table->unsignedBigInteger('type_id')->index();
            $table->integer('score')->default(0);
            $table->string('participant')->default('');
            $table->string('description')->default('');
            $table->timestamps();

            $table->unique(['type_id','participant_id','match_id'],'type_participant_match_unique');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_scores');
    }
};
