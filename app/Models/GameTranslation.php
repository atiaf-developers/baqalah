<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameTranslation extends MyModel {

    protected $table = "games_translations";
    protected $fillable=['title','description'];

 

}
