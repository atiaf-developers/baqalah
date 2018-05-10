<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
class SendFriendRequestNotification extends Notification
{
    use Queueable;
    private $userHasSentRequest;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userHasSentRequest)
    {
        $this->userHasSentRequest=$userHasSentRequest;
    }


    public function via($notifiable)
    {
        return ['database'];
    }

  
    public function toDatabase($notifiable)
    {
        return [
            'id'=>$this->userHasSentRequest->id,
            'user_image'=>$this->userHasSentRequest->user_image,
            'message'=> $this->userHasSentRequest->username.' '._lang('app.friend_request'),
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
