<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $artistId;
    public $queueEntryId;
    public $action; // joined, called, started, completed, cancelled, no_show
    public $status;
    public $queueLength;
    public $estimatedWaitMinutes;
    public $ticket;
    public $timestamp;

    public function __construct(int $artistId, $action = 'updated', $queueEntry = null, int $queueLength = 0, int $estimatedWaitMinutes = 0)
    {
        $this->artistId = $artistId;
        $this->action = $action;
        $this->queueEntryId = $queueEntry ? $queueEntry->id : null;
        $this->status = $queueEntry ? $queueEntry->status : null;
        $this->ticket = $queueEntry ? [
            'id' => $queueEntry->id,
            'queue_number' => $queueEntry->queue_number,
            'client_name' => $queueEntry->client_name,
            'service_name' => $queueEntry->service ? $queueEntry->service->name : '',
            'status' => $queueEntry->status,
            'position' => $queueEntry->position,
            'estimated_wait_minutes' => $queueEntry->estimated_wait_minutes,
        ] : null;
        $this->queueLength = $queueLength;
        $this->estimatedWaitMinutes = $estimatedWaitMinutes;
        $this->timestamp = now()->toIso8601String();
    }

    public function broadcastOn()
    {
        return [
            new Channel('artist-queue.' . $this->artistId),
            new PrivateChannel('admin-queue'),
        ];
    }

    public function broadcastAs()
    {
        return 'QueueUpdated';
    }
}
