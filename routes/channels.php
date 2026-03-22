<?php

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

// Broadcast::channel('chat-message.{chat_id}', function ($user, $chat_id) {
//     return \App\Models\UserConversation::where('conversation_id', $chat_id)
//         ->where(function ($query) use ($user) {
//             $query->where('sender_id', $user->id)
//                 ->orWhere('receiver_id', $user->id);
//         })
//         ->exists();
// }, ['guards' => ['web', 'api'], 'enableClientEvents' => true]);
Broadcast::channel('chat-message.{chat_id}', function ($user, $chat_id) {
    $exists = \App\Models\UserConversation::where('conversation_id', $chat_id)
        ->where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->orWhere('receiver_id', $user->id);
        })
        ->exists();

    Log::info("Broadcast Auth Check for chat-message.$chat_id - User: {$user->id} - Exists: " . ($exists ? 'Yes' : 'No'));

    return $exists;
}, ['guards' => ['api'],'enableClientEvents' => true]);
