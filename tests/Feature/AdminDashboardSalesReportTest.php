<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Service\SalesReportService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Mockery;
use Tests\TestCase;

class AdminDashboardSalesReportTest extends TestCase
{
    public function test_displays_sales_report_provided_by_service(): void
    {
        $admin = new User([
            'first_name' => 'Ivan',
            'last_name' => 'Petrov',
            'email' => 'admin@example.com',
        ]);
        $admin->id = 1;
        $admin->setRelation('roles', new Collection([
            new Role(['slug' => Role::ROLE_ADMIN]),
        ]));

        $service = Mockery::mock(SalesReportService::class);
        $service
            ->shouldReceive('getDashboardReport')
            ->once()
            ->andReturn([
                'total_orders' => 5,
                'successful_orders' => 3,
                'revenue' => '1550.50',
                'canceled_orders' => 1,
                'daily_sales' => [
                    [
                        'date' => Carbon::parse('2026-07-20'),
                        'orders_count' => 3,
                        'revenue' => '1550.50',
                    ],
                ],
            ]);
        $this->app->instance(SalesReportService::class, $service);

        $this
            ->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Всего заказов')
            ->assertSee('5')
            ->assertSee('Успешные продажи')
            ->assertSee('1 550,50 ₽')
            ->assertSee('20.07.2026');
    }
}
