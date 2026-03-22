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
        Schema::create('user_conversation_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('conversation_id');
            $table->unsignedBigInteger('sender_id')->index();
            $table->text('message');
            $table->tinyInteger('status')->default(1)->comment('1=sent, 2=delivered, 3=read');
            $table->boolean('is_deleted')->default(false);
            $table->boolean('is_deleted_both')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('conversation_id')->references('conversation_id')->on('user_conversations')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_conversation_messages');
    }
};
