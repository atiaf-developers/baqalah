<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends MyModel {

    protected $table = "reservations";

    public static function transform($item) {
        $gallery = json_decode($item->gallery);
        //dd($gallery);
        $item->image = url('public/uploads/games/m_' . static::rmv_prefix($gallery[0]));
        $item->url = _url('game/' . $item->slug);

        return $item;
    }

}
