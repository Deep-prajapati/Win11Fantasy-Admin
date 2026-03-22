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
        Schema::table('default_contests', function (Blueprint $table) {
            $table->bigInteger('bot_user')->default(0)->after('match_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('default_contests', function (Blueprint $table) {
            $table->dropColumn('bot_user');
        });
    }
    // ALTER TABLE `default_contests` ADD `bot_user` INT NOT NULL DEFAULT '0' AFTER `match_id`;
};
