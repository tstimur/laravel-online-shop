<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Service\UserNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendWelcomeAfterVerificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * @var array<int, int>
     */
    public array $backoff = [10, 30, 60];

    public function __construct(
        private readonly int $userId,
    ) {
        $this->onQueue('users.notifications.welcome');
    }

    public function handle(UserNotificationService $notificationService): void
    {
        $user = User::query()->find($this->userId);
        if (!$user) {
            return;
        }

        $notificationService->sendWelcome($user);
    }
}
