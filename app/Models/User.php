<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\VerifyEmailNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone
 * @property string $password
 * @property string $status
 * @property string|null $avatar
 *
 * @property-read string $full_name
 *
 * @property Collection|Role[] $roles
 * @property Collection|Order[] $orders
 * @property Collection|Cart[] $carts
 * @property Collection|Address[] $addresses
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use Notifiable;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_BLOCKED = 'blocked';

    public const STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_BLOCKED,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'status',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Связь с заказами */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** Связь с ролями */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /** Связь с корзинами */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /** Связь с адресами */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /** Полное имя */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function hasRole(string $slug): bool
    {
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains('slug', $slug);
        }

        return $this->roles()
            ->where('slug', $slug)
            ->exists();
    }

    /**
     * @param array<int, string> $slugs
     */
    public function hasAnyRole(array $slugs): bool
    {
        if ($slugs === []) {
            return false;
        }

        if ($this->relationLoaded('roles')) {
            return $this->roles->whereIn('slug', $slugs)->isNotEmpty();
        }

        return $this->roles()
            ->whereIn('slug', $slugs)
            ->exists();
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification());
    }
}
