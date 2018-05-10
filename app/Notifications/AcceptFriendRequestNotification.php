<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AcceptFriendRequestNotification extends Notification
{
    use Queueable;

   private $userHasAcceptedRequest;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($userHasSentRequest)
    {
        $this->userHasAcceptedRequest=$userHasSentRequest;
    }


    public function via($notifiable)
    {
        return ['database'];
    }

  
    public function toDatabase($notifiable)
    {
        return [
            'id'=>$this->userHasAcceptedRequest->id,
            'user_image'=>$this->userHasAcceptedRequest->user_image,
            'message'=> $this->userHasAcceptedRequest->username.' '._lang('app.accepted_your_friend_request'),
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
