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
        Schema::create('playerspoints', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('fixture_id')->index();
            $table->bigInteger('team_id')->index();
            $table->bigInteger('player_id')->index();
            $table->bigInteger('points')->index();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playerspoints');
    }
};
