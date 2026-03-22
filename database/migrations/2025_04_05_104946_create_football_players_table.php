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
        Schema::create('football_players', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('player_id')->index();
            $table->unsignedBigInteger('sport_id')->index()->default(1);
            $table->unsignedBigInteger('country_id')->index()->default(0);
            $table->unsignedBigInteger('nationality_id')->index()->default(0);
            $table->string('city_id')->default('');
            $table->unsignedBigInteger('position_id')->index()->default(0);
            $table->unsignedBigInteger('detailed_position_id')->index()->default(0);
            $table->unsignedBigInteger('type_id')->default(0);
            $table->string('common_name')->default('');
            $table->string('firstname')->default('');
            $table->string('lastname')->default('');
            $table->string('name')->default('');
            $table->string('display_name')->default('');
            $table->string('image_path')->default('');
            $table->integer('height')->default(0);
            $table->integer('weight')->default(0);
            $table->date('date_of_birth')->default('1000-01-01');
            $table->string('gender')->default('');

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('football_players');
    }
};

