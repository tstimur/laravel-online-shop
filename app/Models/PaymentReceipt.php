<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $order_payment_id
 * @property string $provider
 * @property string $type
 * @property string $status
 * @property string|null $external_receipt_id
 * @property bool $send_to_customer
 * @property array<string, mixed>|null $request_payload
 * @property array<string, mixed>|null $response_payload
 * @property string|null $error_message
 *
 * @property-read OrderPayment $orderPayment
 */
class PaymentReceipt extends Model
{
    public const TYPE_PAYMENT = 'payment';

    public const STATUS_PENDING = 'pending';
    public const STATUS_REGISTERED = 'registered';
    public const STATUS_SUCCEEDED = 'succeeded';
    public const STATUS_CANCELED = 'canceled';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Чек зарегистрирован',
        self::STATUS_REGISTERED => 'Чек передан вместе с платежом',
        self::STATUS_SUCCEEDED => 'Чек успешно отправлен',
        self::STATUS_CANCELED => 'Чек отменен',
    ];

    protected $fillable = [
        'order_payment_id',
        'provider',
        'type',
        'status',
        'external_receipt_id',
        'send_to_customer',
        'request_payload',
        'response_payload',
        'error_message',
    ];

    protected $casts = [
        'send_to_customer' => 'bool',
        'request_payload' => 'array',
        'response_payload' => 'array',
    ];

    public function orderPayment(): BelongsTo
    {
        return $this->belongsTo(OrderPayment::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }
}
