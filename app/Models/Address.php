<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $city
 * @property string|null $street
 * @property string|null $house
 * @property string|null $apartment
 * @property string|null $label
 * @property bool $is_default
 *
 * @property-read string $full_address
 */
class Address extends Model
{
    protected $fillable = [
        'user_id',
        'city',
        'street',
        'house',
        'apartment',
        'label',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'bool',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->city,
            $this->street,
            $this->house,
            $this->apartment ? 'apt. ' . $this->apartment : null,
        ], static fn ($value) => $value !== null && $value !== '');

        if (!empty($parts)) {
            return implode(', ', $parts);
        }

        return (string) ($this->getRawOriginal('full_address') ?? '');
    }
}
