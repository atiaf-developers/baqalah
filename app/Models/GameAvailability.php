<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameAvailability extends MyModel {

    protected $table = "games_availability";
    protected $fillable=['game_id','date','times'];

 

}
