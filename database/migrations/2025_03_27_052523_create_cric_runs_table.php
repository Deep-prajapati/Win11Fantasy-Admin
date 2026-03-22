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
        Schema::create('cric_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fixture_id')->index();
            $table->unsignedBigInteger('team_id')->index();
            $table->integer('inning');
            $table->integer('score');
            $table->integer('wickets');
            $table->decimal('overs', 5, 1);
            $table->string('pp1')->default('');
            $table->string('pp2')->nullable();
            $table->string('pp3')->nullable();
            $table->timestamps();
            $table->unique(['fixture_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cric_runs');
    }
};
