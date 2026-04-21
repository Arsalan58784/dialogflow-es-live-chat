<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event triggered when a message has been processed by Dialogflow.
 * This class implements ShouldBroadcast to automatically push data to WebSockets.
 */
class MessageProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string The text response from the bot.
     * Public properties are automatically serialized and sent over the socket.
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param string $botText The response text to be broadcasted.
     */
    public function __construct($botText)
    {
        $this->response = $botText;
    }

    /**
     * Specify the channels the event should broadcast on.
     * 
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('chat-channel'), // Public channel for chat updates
        ];
    }
}