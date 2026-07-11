<?php

namespace App\Notifications;

use App\Models\JobMatchLog;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobMatchAlert extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * @param JobMatchLog[] $matches
     */
    public function __construct(
        public array $matches,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $user = $notifiable;
        $count = count($this->matches);

        $message = (new MailMessage)
            ->subject("[CVactive] {$count} việc làm phù hợp với profile của bạn hôm nay")
            ->view('emails.job-match-alert', [
                'user'   => $user,
                'matches' => $this->matches,
                'count'   => $count,
            ]);

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'match_count' => count($this->matches),
            'match_ids'  => array_map(fn($m) => $m->id, $this->matches),
        ];
    }
}
