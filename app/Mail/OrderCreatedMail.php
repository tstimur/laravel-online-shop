<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCreatedMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly Order $order,
    ) {
    }

    public function build(): static
    {
        return $this
            ->subject('Ваш заказ успешно оформлен')
            ->view('emails.order-created')
            ->with([
                'order' => $this->order,
            ]);
    }
}
