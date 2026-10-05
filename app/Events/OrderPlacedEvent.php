<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow; // <-- Must be ShouldBroadcastNow
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlacedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $orderData;

    public function __construct($order)
    {
        $this->orderData = [
            'id'           => $order->id,
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'total'        => number_format($order->total, 3),
            'order_status' => $order->status ?? 'pending',
            'created_at'   => $order->created_at ? $order->created_at->diffForHumans() : 'Just now',
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.placed';
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('admin-orders'),
        ];
    }
}
