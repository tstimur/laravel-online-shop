<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\SendOrderCreatedNotificationJob;
use App\Models\Order;
use App\Models\User;
use App\Service\OrderService;
use App\Service\SessionCartService;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class OrderCreatedNotificationDispatchTest extends TestCase
{
    public function test_dispatches_order_created_notification_job_after_successful_order_creation(): void
    {
        Queue::fake();

        $user = new User([
            'first_name' => 'Ivan',
            'last_name' => 'Petrov',
            'email' => 'ivan@example.com',
            'email_verified_at' => now(),
        ]);
        $user->id = 101;

        $createdOrder = new Order();
        $createdOrder->id = 777;

        $orderService = Mockery::mock(OrderService::class);
        $orderService
            ->shouldReceive('createOrder')
            ->once()
            ->with($user, Order::PAYMENT_METHOD_CASH, Mockery::type(SessionCartService::class))
            ->andReturn($createdOrder);

        $this->app->instance(OrderService::class, $orderService);
        $this->app->instance(SessionCartService::class, new SessionCartService());

        $response = $this
            ->actingAs($user)
            ->post(route('orders.store'), [
                'payment_method' => Order::PAYMENT_METHOD_CASH,
            ]);

        $response
            ->assertRedirect(route('orders.index'))
            ->assertSessionHas('success', 'Заказ создан.');

        Queue::assertPushed(SendOrderCreatedNotificationJob::class, 1);
    }
}
