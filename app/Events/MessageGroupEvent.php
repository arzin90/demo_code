<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageGroupEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $message;

    public $params;

    public function __construct($message, $params = [])
    {
        $this->message = $message;
        $this->params = $params;
    }

    public function broadcastOn()
    {
        return [sprintf('group-%d', $this->message->group_id)];
    }

    public function broadcastAs()
    {
        return 'message';
    }
}
