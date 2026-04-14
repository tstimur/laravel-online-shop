<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly User $user,
    ) {
    }

    /**
     * Собирает письмо и передаёт данные в шаблон.
     */
    public function build(): static
    {
        return $this
            ->subject('Добро пожаловать в интернет-магазин')
            ->view('emails.welcome')
            ->with([
                'user' => $this->user,
            ]);
    }
}
