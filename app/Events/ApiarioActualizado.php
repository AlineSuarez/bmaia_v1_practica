<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ApiarioActualizado implements ShouldBroadcast
{
    public function __construct(public $apiario, public $userId) {}

    public function broadcastOn()
    {
        // canal por usuario
        return new Channel('user.'.$this->userId);
    }

    public function broadcastAs()
    {
        return 'apiario.updated';
    }

    public function broadcastWith()
    {
        return $this->apiario->toArray();
    }
}
