<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SendMessageEvent implements ShouldBroadcastNow
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat_id;
    public $message;
    public $senderId;
    public $receiverId;
    public $is_system_broadcast;

    public function __construct($chat_id,$message, $senderId,$receiverId,$is_system_broadcast = false)
    {
        $this->chat_id = $chat_id;
        $this->message = $message;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->is_system_broadcast = $is_system_broadcast;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("chat-message.{$this->chat_id}");
        // return new PresenceChannel("chat-message.{$this->chat_id}");
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message,
            'sender_id' => $this->senderId,
            'receiver_id' => $this->receiverId,
            'is_system_broadcast' => $this->is_system_broadcast,
        ];
    }
    public function broadcastAs()
    {
        return 'chatMessage';
    }
}
