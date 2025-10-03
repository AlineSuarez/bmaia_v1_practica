<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class ColmenaActualizada implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public array $colmena;
    public int $userId;

    public function __construct(array $colmena, int $userId)
    {
        $this->colmena = $colmena;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return ['user.'.$this->userId];
    }

    public function broadcastAs()
    {
        return 'colmena.updated';
    }
}
