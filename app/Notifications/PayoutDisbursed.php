<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PayoutDisbursed extends Notification
{
    use Queueable;

    private array $payload;

    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    public function via($notifiable)
    {
        $channels = ['database'];
        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toArray($notifiable)
    {
        return $this->payload;
    }

    public function toMail($notifiable)
    {
        $p = $this->payload;

        return (new MailMessage)
            ->subject('Pencairan Dana: ' . ($p['seller_order_number'] ?? ''))
            ->greeting('Halo ' . ($notifiable->name ?? 'Penjual'))
            ->line('Dana untuk seller order ' . ($p['seller_order_number'] ?? '#') . ' telah dicairkan.')
            ->line('Nominal: Rp ' . number_format($p['amount'] ?? 0, 0, ',', '.'))
            ->line('Sumber: ' . ($p['source'] ?? '-'))
            ->line('Tanggal: ' . ($p['disbursed_at'] ?? now()->toDateTimeString()))
            ->line('Terima kasih.');
    }
}
