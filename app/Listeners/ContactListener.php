<?php

namespace App\Listeners;

use App\Events\ContactEvent;
use App\Notifications\ContactCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class ContactListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
      dd("contact listener");
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ContactEvent $event): void
    {
        dd("contact"); 
        // $user = Auth::user();
        // $user->notify(new ContactCreatedNotification($event->contact));
    }
}