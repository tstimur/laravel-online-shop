<?php

declare(strict_types=1);

namespace App\Service;

use App\Mail\OrderCreatedMail;
use App\Mail\WelcomeMail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Mail\Mailer;

class UserNotificationService
{
    public function __construct(
        private readonly Mailer $mailer,
    ) {
    }

    /**
     * Отправляет письмо с подтверждением email.
     */
    public function sendEmailVerification(User $user): void
    {
        $user->sendEmailVerificationNotification();
    }

    /**
     * Отправляет приветственное письмо после успешного подтверждения email.
     */
    public function sendWelcome(User $user): void
    {
        $this->mailer
            ->to($user->email, $user->full_name)
            ->send(new WelcomeMail($user));
    }

    /**
     * Отправляет письмо с деталями успешно оформленного заказа.
     */
    public function sendOrderCreated(Order $order): void
    {
        $this->mailer
            ->to($order->user->email, $order->user->full_name)
            ->send(new OrderCreatedMail($order));
    }
}
