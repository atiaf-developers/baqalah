<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends MyModel {

    protected $table = 'slider';
    public static $sizes = array(
        's' => array('width' => 400, 'height' => 200),
        'l' => array('width' => 500, 'height' => 200),
    );

    public static function  transform($item)
    {
       
        $item->image = url('public/uploads/slider/l_'.static::rmv_prefix($item->image));
        
        return $item;

    }
    public static function  transformFrontHome($item)
    {
       
        $item->image = url('public/uploads/slider/l_'.static::rmv_prefix($item->image));
        
        return $item;

    }
    
   


}
