<?php

namespace App\Providers;

use Livewire\Livewire;
use App\Events\ChatEvent;
use App\Events\ChatMessageSent;
use Illuminate\Support\Facades\Event;
use App\Listeners\ChatMessageListener;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Event::listen(
        //     // ChatMessageListener::class,
        // );
    }
}
