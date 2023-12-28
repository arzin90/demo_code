<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RequestGroupMessageEvent implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $group_member;
    public $message;

    public function __construct($group_member, $message)
    {
        $this->group_member = $group_member;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return [sprintf('group-message-%d', $this->group_member)];
    }

    public function broadcastAs()
    {
        return 'request';
    }
}
