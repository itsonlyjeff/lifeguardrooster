<?php

namespace App\Notifications;

use App\Models\Shift;
use App\Models\ShiftSchedule;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Spatie\CalendarLinks\Link;

class UserIsPlannedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private ShiftSchedule $schedule;
    private Shift $shift;
    Private Link $link;
    private User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(ShiftSchedule $schedule, Shift $shift, User $user)
    {
        $this->schedule = $schedule->load(['user', 'role']);
        $this->shift = $shift->load(['shiftschedules.user', 'shiftschedules.role']);
        $this->user = $user;
        $this->link = $this->buildLink();
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
        $icsData = base64_decode(substr($this->link->ics(), strlen('data:text/calendar;charset=utf8;base64,')));

        return (new MailMessage)
            ->subject($this->buildMailSubject())
            ->attachData($icsData, 'event.ics', ['mime' => 'text/calendar'])
            ->view('mails.user-is-planned', [
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
     * Build the Link for the Notification.
     *
     * @return Link
     */
    private function buildLink(): Link
    {
        return Link::create(ucfirst($this->schedule->role) . ' - ' . $this->shift->name, $this->shift->start, $this->shift->end)
            ->address('Oever, Hellevoetsluis');
    }

    /**
     * Build the Subject for the Mail.
     *
     * @return string
     */
    private function buildMailSubject(): string
    {
        return ucfirst(trans('days.' . $this->shift->start->format('l'))) . ' ' . $this->shift->start->format('d-m-Y') . ' - ' . 'U bent ingeroosterd.';
    }
}
