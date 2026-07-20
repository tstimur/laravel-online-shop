<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property \Illuminate\Support\Carbon $report_date
 * @property int $total_orders
 * @property int $successful_orders
 * @property string $revenue
 * @property int $canceled_orders
 * @property \Illuminate\Support\Carbon $generated_at
 */
class DailySalesReport extends Model
{
    protected $fillable = [
        'report_date',
        'total_orders',
        'successful_orders',
        'revenue',
        'canceled_orders',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'revenue' => 'decimal:2',
            'generated_at' => 'datetime',
        ];
    }
}
