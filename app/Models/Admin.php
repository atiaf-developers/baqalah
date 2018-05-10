<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable {

    use Notifiable;

    protected $table = "admins";

    public function group() {
        return $this->belongsTo('App\Models\Group', 'group_id', 'id');
    }



}
