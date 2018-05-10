<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use App\Models\Report;

class Noti implements ShouldBroadcastNow {

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $user_id;
    public $title;
    public $body;
    public $type;
    public $url;

    public function __construct($data) {
        $this->user_id = $data['user_id'];
        $this->type = $data['type'];
        $this->body = $data['body'];
        $this->url = $data['url'];
        $this->title = isset($data['title']) ? $data['title'] : env('APP_NAME');
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn() {
        return ['new_noti'];
    }

}
