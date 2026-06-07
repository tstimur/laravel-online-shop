<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Order;
use App\Service\UserNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendOrderCreatedNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * @var array<int, int>
     */
    public array $backoff = [10, 30, 60];

    public function __construct(
        private readonly int $orderId,
    ) {
        $this->onQueue('orders.notifications.created');
    }

    public function handle(UserNotificationService $notificationService): void
    {
        $order = Order::query()
            ->with(['user', 'items.product'])
            ->find($this->orderId);

        if (!$order) {
            return;
        }

        $notificationService->sendOrderCreated($order);
    }
}
