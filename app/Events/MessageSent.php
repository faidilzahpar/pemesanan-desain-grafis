<?php

namespace App\Events;

use App\Models\Chat;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;

    public function __construct(Chat $chat)
    {
        $this->chat = $chat;
    }

    public function broadcastOn()
    {
        // Channel privat per Order ID
        return new PrivateChannel('order.' . $this->chat->order_id);
    }
    
    public function broadcastWith()
    {
        // Pastikan load relasi dulu sebelum dikirim
        $this->chat->load('referencedFile');
        
        return [
            'id' => $this->chat->id,
            'message' => $this->chat->message,
            'attachment' => $this->chat->attachment,
            'referenced_file' => $this->chat->referencedFile, // Kirim object context
            'user_id' => $this->chat->user_id,
            'user_name' => $this->chat->user->name,
            'created_at' => $this->chat->created_at->toISOString(),
        ];
    }
}