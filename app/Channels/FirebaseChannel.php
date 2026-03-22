<?php

namespace App\Channels;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Notifications\Notification as LaravelNotification;

class FirebaseChannel
{
    public function send($notifiable, LaravelNotification $notification)
    {
        if (!method_exists($notification, 'toFirebase')) {
            return;
        }

        $messageData = $notification->toFirebase($notifiable);
        $firebase = (new Factory())->withServiceAccount(storage_path(config('services.firebase.credentials')));
        $messaging = $firebase->createMessaging();
        $message = CloudMessage::withTarget('topic', $messageData['topic'])
            ->withNotification(Notification::create($messageData['title'], $messageData['body']))
            ->withData($messageData['data']);
        $messaging->send($message);
    }
}
