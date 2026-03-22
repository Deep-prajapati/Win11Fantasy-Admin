<?php

namespace App\Providers;

use App\Events\ChatMessageSent;
use App\Listeners\ChatMessageListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    // protected $listen = [
    //     Registered::class => [
    //         SendEmailVerificationNotification::class,
    //     ],

    // ];

    protected $listen = [
        \Laravel\Reverb\Events\MessageReceived::class => [
            \App\Listeners\HandleMessageReceived::class,
        ],
        // ChatMessageSent::class => [
        //     ChatMessageListener::class,
        // ],
    ];
    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
