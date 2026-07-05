<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $order_id
 * @property string $provider
 * @property string $status
 * @property string $amount
 * @property string $currency
 * @property string|null $external_payment_id
 * @property string|null $idempotence_key
 * @property string|null $confirmation_url
 * @property array<string, mixed>|null $request_payload
 * @property array<string, mixed>|null $response_payload
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $canceled_at
 *
 * @property-read Order $order
 */
class OrderPayment extends Model
{
    public const PROVIDER_YOOKASSA = 'yookassa';

    public const STATUS_PENDING = 'pending';
    public const STATUS_WAITING_FOR_CAPTURE = 'waiting_for_capture';
    public const STATUS_SUCCEEDED = 'succeeded';
    public const STATUS_CANCELED = 'canceled';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Ожидает подтверждения оплаты',
        self::STATUS_WAITING_FOR_CAPTURE => 'Ожидает списания',
        self::STATUS_SUCCEEDED => 'Оплата подтверждена',
        self::STATUS_CANCELED => 'Платеж отменен',
    ];

    protected $fillable = [
        'order_id',
        'provider',
        'status',
        'amount',
        'currency',
        'external_payment_id',
        'idempotence_key',
        'confirmation_url',
        'request_payload',
        'response_payload',
        'error_message',
        'paid_at',
        'canceled_at',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'paid_at' => 'datetime',
        'canceled_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function latestReceipt(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(PaymentReceipt::class)->latestOfMany();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
