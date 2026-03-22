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
        Schema::create('recharges', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('method')->default(1)->comment('1=manual,2=auto');
            $table->unsignedBigInteger('user_id')->index();
            $table->float('amount', 10, 2)->default(0.00);
            $table->string('order_id');
            $table->string('tnx_id');
            $table->tinyInteger('status')->default(1)->comment('1=pending,2=approved,3=cancelled');
            $table->string('image')->nullable();
            $table->string('utr_no')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recharges');
    }
};
