<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class VisitaActualizada implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public array $visita;
    public int $userId;

    public function __construct(array $visita, int $userId)
    {
        $this->visita = $visita;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return ['user.'.$this->userId];
    }

    public function broadcastAs()
    {
        return 'visita.updated';
    }
}
