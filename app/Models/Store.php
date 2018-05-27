<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends MyModel {

    protected $table = "stores";
    protected $casts = ['id' => 'integer','lat' => 'double','lng' => 'double'];
    public static $sizes = array(
        's' => array('width' => 300, 'height' => 300),
        'm' => array('width' => 400, 'height' => 400),
    );

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
        $transformer->orders_notify = $item->orders_notify;
        
        if (isset($extra_params['user']) && $extra_params['user']->type == 1) {
            $transformer->categories = implode(" - ",$item->categories()->join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
            ->where('categories_translations.locale', $lang_code)
            ->where('categories.active', true)
            ->pluck('categories_translations.title')->toArray());
            $transformer->available_text = $item->available == 0 ? _lang('app.closed') : _lang('app.opened');
            $transformer->number_of_products = $item->number_of_products;
            $transformer->rate = $item->rate;
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
