<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends MyModel
{
	protected $table = "ratings";
	protected $fillable = ['user_id','store_id','rate'];


	
}
