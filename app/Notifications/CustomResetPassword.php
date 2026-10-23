<?php

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from('noreply@sharpedge.xinksoft.site', 'SharpEdge')
            ->subject('Reset Your Password')
            ->line('You requested a password reset.')
            ->action('Reset Password', url('/reset-password'))
            ->line('If not you, ignore this email.');
    }
}

