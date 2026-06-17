<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminTransactionCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $adminUserIds;
    public $payload;

    public function __construct(array $adminUserIds, array $payload)
    {
        $this->adminUserIds = $adminUserIds;
        $this->payload = $payload;
    }

    public function broadcastOn()
    {
        return collect($this->adminUserIds)->map(function ($adminUserId) {
            return new PrivateChannel('App.User.' . $adminUserId);
        })->all();
    }

    public function broadcastAs()
    {
        return 'admin.transaction.created';
    }

    public function broadcastWith()
    {
        return $this->payload;
    }
}
