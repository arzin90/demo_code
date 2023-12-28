<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageCountEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $unread_all_count;
    public $to;

    public function __construct($unread_all_count, $to)
    {
        $this->unread_all_count = $unread_all_count;
        $this->to = $to;
    }

    public function broadcastOn()
    {
        return [sprintf('message-%d-unread-all-count', $this->to)];
    }

    public function broadcastAs()
    {
        return 'unread_all_count';
    }
}
