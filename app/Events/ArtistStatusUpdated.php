<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArtistStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $artistId;
    public $artistName;
    public $liveStatus;
    public $queueLength;
    public $estimatedWaitMinutes;
    public $timestamp;

    public function __construct(int $artistId, string $artistName, string $liveStatus, int $queueLength = 0, int $estimatedWaitMinutes = 0)
    {
        $this->artistId = $artistId;
        $this->artistName = $artistName;
        $this->liveStatus = $liveStatus;
        $this->queueLength = $queueLength;
        $this->estimatedWaitMinutes = $estimatedWaitMinutes;
        $this->timestamp = now()->toIso8601String();
    }

    public function broadcastOn()
    {
        return [
            new Channel('artist-status'),
            new PrivateChannel('admin-queue'),
        ];
    }

    public function broadcastAs()
    {
        return 'ArtistStatusUpdated';
    }
}
