<?php

namespace App\Providers;

use App\Events\ChatMessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class ClientEventServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Listen for client events and broadcast them to others
        Event::listen('chat-message', function ($data) {
            $message = $data['message'];
            $senderId = $data['sender_id'];
            $receiverId = $data['receiver_id'];
            \App\Models\ChatMessage::create([
                'content' => $message,
                'sender_id' => $senderId,
                'receiver_id' => $receiverId,
                'message_type'=>'text',
            ]);
            broadcast(new ChatMessageSent($message, $senderId, $receiverId));
        });
    }
}
