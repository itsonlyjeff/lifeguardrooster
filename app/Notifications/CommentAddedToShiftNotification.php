<?php

namespace App\Notifications;

use Filament\Notifications\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Filament\Notifications\Notification as FilamentNotification;

class CommentAddedToShiftNotification extends Notification
{
    use Queueable;

    public $comment;
    public $shift;

    /**
     * Create a new notification instance.
     */
    public function __construct($comment, $shift)
    {
        $this->comment = $comment;
        $this->shift = $shift;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable): array
    {
        return FilamentNotification::make()
            ->title('Nieuwe reactie toegevoegd')
            ->info()
            ->body("{$this->comment->sender->name} heeft een reactie toegevoegd op de dienst: {$this->shift->name}.")
            ->actions([
                Action::make('Bekijk reactie')
                    ->button()
                    ->url(route('filament.app.pages.shift.{id}', ['tenant' => Filament::getTenant(), 'id' => $this->shift->id]))
                    ->markAsRead(true)
            ])
            ->getDatabaseMessage();
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
}
