<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingTranslation extends MyModel {

    protected $table = "settings_translations";
    protected $fillable=['locale','title','description','about','address','policy'];
    protected $hidden = array('id','locale','created_at','updated_at');

 

}
