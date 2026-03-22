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
        Schema::create('officials', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('official_id')->unique()->comment('External API country ID');
            $table->bigInteger('country_id');
            $table->string('first_name');
            $table->string('lastname');
            $table->enum('gender', ['m', 'f'])->nullable();
            $table->timestamp('dateofbirth')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officials');
    }
};
