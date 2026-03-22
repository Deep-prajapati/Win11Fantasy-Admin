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
        Schema::table('users', function (Blueprint $table) {
            $table->string('country_code')->default('+91');
            $table->string('mobile_number');
            $table->string('image')->default('assets/default.png');
            $table->boolean('is_banned')->default(false);
            $table->boolean('is_kyc_complete')->default(false);
            $table->tinyInteger('role')->default(2)->comment('1=admin,2=user,3=bot user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'country_code',
                'mobile_number',
                'image',
                'is_banned',
                'is_kyc_complete',
                'role',
            ]);
        });
    }
};
