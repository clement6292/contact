<?php

namespace App\Events;

use App\Models\Contact;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ContactEvent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public Contact $contact)
    {
        // dd($contact);
    }
  

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('contact'),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'contact' => [
                'id' => $this->contact->id,
                'name' => $this->contact->name,
                'email' => $this->contact->email,
                'phone' => $this->contact->phone,
                'company' => $this->contact->company,
                'country' => $this->contact->country,
                'country_fly' => $this->contact->country_fly,
                'created_at' => $this->contact->created_at,
            ],
        ];
    }
}