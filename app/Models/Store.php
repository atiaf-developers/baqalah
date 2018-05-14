<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends MyModel {

    protected $table = "stores";

    public static $sizes = array(
        's' => array('width' => 300, 'height' => 300),
        'm' => array('width' => 400, 'height' => 400),
    );

    public function categories()
    {
        return $this->belongsToMany(Category::class,'store_categories','store_id','category_id');
    }

    public static function transform($item)
    {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->name = $item->name;
        $transformer->description = $item->description;
        $transformer->image = url('public/uploads/stores').'/'.$item->image;
        $transformer->phone = $item->mobile;
        $transformer->lat = $item->lat;
        $transformer->lng = $item->lng;
        $transformer->address = $item->address;
        $transformer->available = $item->available;
        if ($user = static::auth_user()) {
            if ($user->type == 1) {
                $transformer->available_text = $item->available == 0 ? _lang('app.closed') : _lang('app.opened');
                $transformer->is_rated = $item->is_rated ? 1 : 0;
                $transformer->number_of_products = $item->number_of_products;
                $transformer->rate = $item->rate;
            }  
        }
        return $transformer;
    }



    protected static function boot() {
        parent::boot();

        static::deleting(function($store) {
          
        });

        static::deleted(function($store) {
            $old_images = $store->image;
            static::deleteUploaded('stores', $old_images);  
        });
    }

}
