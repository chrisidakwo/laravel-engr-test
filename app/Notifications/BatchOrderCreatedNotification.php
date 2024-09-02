<?php

namespace App\Notifications;

use App\Models\Batch;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BatchOrderCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Order $order, public Batch $batch)
    {
        //
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
            ->subject('New Batch Order')
            ->greeting("Dear {$notifiable->name},")
            ->line("A new batch order has been processed for you. See the details below:")
            ->line("**Provider name**: {$this->order->provider_name}")
            ->line("**Batch**: {$this->batch->name}")
            ->line('Click on the button below to view all orders in this batch')
            ->action('View batch orders', '')
            ->salutation('Cheers!');
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
