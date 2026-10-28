<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InquiryEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $inquiryId;
    public $artistId;
    public $userId;
    public $action; // 'created', 'message_sent', 'status_changed'
    public $title;
    public $message;
    public $timestamp;

    public function __construct(int $inquiryId, int $artistId, int $userId, string $action, string $title, string $message)
    {
        $this->inquiryId = $inquiryId;
        $this->artistId = $artistId;
        $this->userId = $userId;
        $this->action = $action;
        $this->title = $title;
        $this->message = $message;
        $this->timestamp = now()->toIso8601String();
    }

    public function broadcastOn()
    {
        return [
            new Channel('artist-inquiries.' . $this->artistId),
            new Channel('user-inquiries.' . $this->userId),
            new Channel('admin-inquiries'),
        ];
    }

    public function broadcastAs()
    {
        return 'InquiryEvent';
    }
}
