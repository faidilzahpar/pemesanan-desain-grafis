<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderId;
    public $readerId;

    // Kita kirim Order ID dan ID orang yang membaca pesan
    public function __construct($orderId, $readerId)
    {
        $this->orderId = $orderId;
        $this->readerId = $readerId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('order.' . $this->orderId);
    }
}