<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Group extends MyModel
{
    protected $table = "groups";
    
    public function admin() {
        return $this->hasMany('App\Models\Admin');
    }
}
