<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Store extends MyModel {
    
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = "stores";
    protected $casts = ['id' => 'integer','lat' => 'double','lng' => 'double'];
    public static $sizes = array(
        's' => array('width' => 900, 'height' => 500),
        'm' => array('width' => 900, 'height' => 500),
    );
    
    public function user()
    {
       return  $this->belongsTo(User::class,'user_id');
    }
    public function products()
    {
       return  $this->hasMany(Product::class,'store_id');
    }
    public function categories() {
        return $this->belongsToMany(Category::class, 'store_categories', 'store_id', 'category_id');
    }

    public static function transform($item, $extra_params = array()) {
      
        $lang_code = static::getLangCode();
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->name = $item->name;
        $transformer->description = $item->description;
        $transformer->image = url('public/uploads/stores') . '/' . $item->image;
        $transformer->phone = $item->mobile;
        $transformer->lat = $item->lat;
        $transformer->lng = $item->lng;
        $transformer->address = $item->address;
        $transformer->available = $item->available;
        if (isset($item->orders_notify)) {
            $transformer->orders_notify = $item->orders_notify;
        }
        if ((isset($extra_params['user']) && $extra_params['user']->type == 1) || !isset($extra_params['user'])) {
            $transformer->categories = implode(" - ",$item->categories()->join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
            ->where('categories_translations.locale', $lang_code)
            ->where('categories.active', true)
            ->pluck('categories_translations.title')->toArray());
            $transformer->available_text = $item->available == 0 ? _lang('app.closed') : _lang('app.opened');
            $transformer->number_of_products = $item->number_of_products;
            $transformer->rate = $item->rate;
            $transformer->is_rated = $item->is_rated != 0 ? 1 : 0;
        }

        return $transformer;
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($store) {
            foreach ($store->products as $product) {
                $product->delete();
            } 
        });

        static::deleted(function($store) {
            $old_image = $store->image;
            static::deleteUploaded('stores', $old_image);
            $store->user->delete();
        });
    }

}
