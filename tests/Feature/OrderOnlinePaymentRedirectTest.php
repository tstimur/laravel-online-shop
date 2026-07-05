<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\SendOrderCreatedNotificationJob;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\User;
use App\Service\OrderService;
use App\Service\SessionCartService;
use App\Service\YooKassaPaymentService;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;

class OrderOnlinePaymentRedirectTest extends TestCase
{
    public function test_redirects_to_yookassa_after_online_order_creation(): void
    {
        Queue::fake();

        $user = new User([
            'first_name' => 'Ivan',
            'last_name' => 'Petrov',
            'email' => 'ivan@example.com',
            'email_verified_at' => now(),
        ]);
        $user->id = 101;

        $createdOrder = new Order([
            'payment_method' => Order::PAYMENT_METHOD_YOOKASSA,
            'status' => Order::STATUS_PENDING,
        ]);
        $createdOrder->id = 777;

        $createdPayment = new OrderPayment([
            'confirmation_url' => 'https://pay.yookassa.ru/mock-confirmation',
        ]);

        $orderService = Mockery::mock(OrderService::class);
        $orderService
            ->shouldReceive('createOrder')
            ->once()
            ->with($user, Order::PAYMENT_METHOD_YOOKASSA, Mockery::type(SessionCartService::class))
            ->andReturn($createdOrder);

        $paymentService = Mockery::mock(YooKassaPaymentService::class);
        $paymentService
            ->shouldReceive('createPaymentForOrder')
            ->once()
            ->with($createdOrder)
            ->andReturn($createdPayment);

        $this->app->instance(OrderService::class, $orderService);
        $this->app->instance(YooKassaPaymentService::class, $paymentService);
        $this->app->instance(SessionCartService::class, new SessionCartService());

        $response = $this
            ->actingAs($user)
            ->post(route('orders.store'), [
                'payment_method' => Order::PAYMENT_METHOD_YOOKASSA,
            ]);

        $response->assertRedirect('https://pay.yookassa.ru/mock-confirmation');

        Queue::assertPushed(SendOrderCreatedNotificationJob::class, 1);
    }
}
