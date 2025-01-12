<?php

namespace App\Notifications;

use App\Models\Shift;
use App\Models\ShiftSchedule;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserIsDeletedFromShiftNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private ShiftSchedule $schedule;
    private Shift $shift;
    private User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(ShiftSchedule $schedule, Shift $shift, User $user)
    {
        $this->schedule = $schedule->load(['user', 'role']);
        $this->shift = $shift->load(['shiftschedules.user', 'shiftschedules.role']);
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->buildMailSubject())
            ->view('mails.user-is-deleted-from-shift', [
                'schedule' => $this->schedule,
                'shift' => $this->shift,
                'shiftschedules' => $this->shift->shiftschedules,
                'user' => $this->user,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    /**
     * Build the Subject for the Mail.
     *
     * @return string
     */
    private function buildMailSubject(): string
    {
        return ucfirst(trans('days.' . $this->shift->start->format('l'))) . ' ' . $this->shift->start->format('d-m-Y') . ' - ' . 'Uw dienst is geannuleerd.';
    }
}
