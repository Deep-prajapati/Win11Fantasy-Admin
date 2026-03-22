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
        Schema::create('contest_types', function (Blueprint $table) {
            $table->id();
            $table->string('contest_type')->nullable();
            $table->text('description')->nullable();
            $table->integer('max_entries')->nullable();
            $table->integer('free_wheel_count')->default(0);
            $table->string('cancellable')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contest_types');
    }
};
