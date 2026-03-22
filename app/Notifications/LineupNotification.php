<?php

namespace App\Notifications;

use App\Channels\FirebaseChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FCMNotification;
use Kreait\Firebase\Factory;

class LineupNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $body;
    protected $data;

    public function __construct($title, $body, $data = [])
    {
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    public function via($notifiable)
    {
        return [FirebaseChannel::class];
    }
    public function toFirebase($notifiable)
    {
        $firebase = (new Factory())->withServiceAccount(storage_path(config('services.firebase.credentials')));

        $messaging = $firebase->createMessaging();

        $topic = 'match_lineup';

        $message = CloudMessage::withTarget('topic', $topic)
            ->withNotification(FCMNotification::create($this->title, $this->body))
            ->withData($this->data);

         $messaging->send($message);
         return [
            'topic' => 'match_lineup',
            'title' => $this->title,
            'body' => $this->body,
            'data' => $this->data,
        ];
    }
}
