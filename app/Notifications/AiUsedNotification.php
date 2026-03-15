<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

final class AiUsedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $caller,
        private readonly string $aiClient,
        private readonly int $tokens,
        private readonly string $message,
    ) {}

    public function via(string $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(string $notifiable): array
    {
        return [
            'caller' => $this->caller,
            'ai-client' => $this->aiClient,
            'tokens' => $this->tokens,
            'message' => $this->message,
        ];
    }

    public function toArray(string $notifiable): array
    {
        return [
            'caller' => $this->caller,
            'ai-client' => $this->aiClient,
            'tokens' => $this->tokens,
            'message' => $this->message,
        ];
    }
}
