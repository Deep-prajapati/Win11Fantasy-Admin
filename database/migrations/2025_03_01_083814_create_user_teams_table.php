<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('match_id')->index();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('team_id');
            $table->unsignedBigInteger('caption_id');
            $table->unsignedBigInteger('voic_caption_id');
            $table->longText('teams');
            $table->string('team_count');
            $table->boolean('team_joined_status')->default(false);
            $table->float('points',8,2)->default(0);
            $table->bigInteger('rank')->default(0);
            $table->boolean('is_winning')->default(false);
            $table->float('price',8,2)->default(0);
            $table->integer('edit_count')->default(1);
            $table->timestamp('team_create_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('team_update_time')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_teams');
    }
};
