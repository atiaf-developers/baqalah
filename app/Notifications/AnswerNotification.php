<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AnswerNotification extends Notification
{
    use Queueable;
    private $userHasAnswered;
    private $question;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($question,$userHasAnswered)
    {
        $this->question=$question;
        $this->userHasAnswered=$userHasAnswered;
    }


    public function via($notifiable)
    {
        return ['database'];
    }

  
    public function toDatabase($notifiable)
    {
        //dd($this->userHasSentComment->user_image);
        return [
            'id'=>$this->question->id,
            'name'=> $this->userHasAnswered->name,
            'message'=> $this->question->question,
            'prize'=> $this->question->prize,
            'type'=>2
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
