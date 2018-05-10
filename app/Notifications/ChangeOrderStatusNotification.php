<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ChangeOrderStatusNotification extends Notification {

    use Queueable;

    private $order;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order) {
        $this->order = $order;
    }

    public function via($notifiable) {
        return ['database'];
    }

    public function toDatabase($notifiable) {
        //dd($this->userHasSentComment->user_image);

        $noti = [
            'id' => $this->order->id,
            'title' => $this->order->category_title,
            'message' => $this->order->status_text,
            'type' => 1
        ];
        if (isset($this->order->status_no)) {
            $noti['status_no'] = $this->order->status_no;
        }
        return $noti;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable) {
        return [
                //
        ];
    }

}
