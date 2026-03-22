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
        Schema::create('football_leagues', function (Blueprint $table) {
            $table->id(); // 'id' (primary key)
            $table->unsignedBigInteger('league_id')->index();
            $table->unsignedBigInteger('sport_id')->default(1)->index();
            $table->unsignedBigInteger('country_id')->index();
            $table->string('name')->default('');
            $table->boolean('active')->default(1);
            $table->string('short_code')->default('');
            $table->string('image_path')->default('');
            $table->string('type')->default('');
            $table->string('sub_type')->default('');
            $table->timestamp('last_played_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('football_leagues');
    }
};
