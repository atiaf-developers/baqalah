<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Device extends MyModel
{
    protected $table = "devices";
    protected $fillable = ['device_id','device_type','device_token'];
   
}
