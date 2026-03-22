<?php

namespace App\Listeners;

use App\Events\ChatMessageSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Reverb\Events\MessageReceived;

class HandleMessageReceived
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     *
     *
     * @param  \Laravel\Reverb\Events\MessageReceived  $event
     * @return void
     */
    public function handle(MessageReceived $event)
    {
        // Log the received event
        // \Log::info('i am here @@@@@@@@@@@');
        // \Log::info(json_encode($event));

        // Ensure $event->message is a string before decoding
        // $message = is_string($event->message) ? json_decode($event->message) : $event->message;

        // if (!isset($message->data)) {
        //     \Log::error('Message data is missing.');
        //     return;
        // }

        // $data = is_string($message->data) ? json_decode($message->data) : $message->data;

        // if (isset($message->event) && $message->event === 'send-chat-message') {
        //     \App\Models\ChatMessage::create([
        //         'message' => $data->message,
        //         'sender_id' => $data->sender_id,
        //         'receiver_id' => $data->receiver_id,
        //         'message_type' => 'text',
        //     ]);
        //     if (!isset($data->is_system_broadcast) || !$data->is_system_broadcast) {
        //         // Broadcast to the receiver's private channel
        //         broadcast(new ChatMessageSent(
        //             $data->message,
        //             $data->sender_id,
        //             $data->receiver_id,
        //             true
        //         ))->toOthers();

        //         \Log::info('Broadcasted to receiver');
        //     }
        //     \Log::info('send-chat-message.........insert....');
        // }
    }
}
