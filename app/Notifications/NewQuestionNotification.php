<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewQuestionNotification extends Notification
{
    use Queueable;
    private $famous;
    private $question;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($question,$famous)
    {
        $this->question=$question;
        $this->famous=$famous;
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
            'name'=> $this->famous->name,
            'message'=> $this->question->question,
            'type'=>1
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
