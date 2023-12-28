<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRemoveEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $message_id;

    public $from;

    public $to;

    public function __construct($message_id, $from, $to)
    {
        $this->message_id = $message_id;
        $this->from = $from;
        $this->to = $to;
    }

    public function broadcastOn()
    {
        return [sprintf('remove-message-%d-%d-%d', $this->to, $this->from, $this->message_id)];
    }

    public function broadcastAs()
    {
        return 'message';
    }
}
