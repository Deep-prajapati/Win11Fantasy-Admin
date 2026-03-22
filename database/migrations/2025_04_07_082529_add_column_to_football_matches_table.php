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
        Schema::table('football_matches', function (Blueprint $table) {
            $table->boolean('is_upcomming')->default(false);
            $table->boolean('is_live')->default(false);
            $table->boolean('is_completed')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_prize_distributed')->default(false);
            $table->boolean('is_prize_refunded')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('football_matches', function (Blueprint $table) {
            $table->dropColumn([
                'is_upcomming',
                'is_live',
                'is_completed',
                'is_cancelled',
                'is_prize_distributed',
                'is_prize_refunded',
            ]);
        });
    }
};
