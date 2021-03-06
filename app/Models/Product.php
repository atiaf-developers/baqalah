<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Product extends MyModel
{
    use SoftDeletes;
    
    protected $table = "products";
    protected $dates = ['deleted_at'];

    protected $casts = ['price' => 'float','quantity' => 'integer','store_id' => 'integer','store_rate' => 'double',
    'store_available' => 'int'];

    public static $sizes = array(
        's' => array('width' => 300, 'height' => 300),
        'm' => array('width' => 900, 'height' => 500),
    );
    
    
    public function store() {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }


    public static function transform($item)
    {
            $user = static::auth_user();
            $transformer = new \stdClass();

            $transformer->id = $item->id;
            $transformer->name = $item->name;
            $transformer->description = $item->description;
            $transformer->price = $item->price;
            $transformer->quantity = $item->quantity;
            
            $prefixed_array = array();
            $product_images =  json_decode($item->images);
            if (count($product_images) > 0) {
                foreach ($product_images as $key => $value) {
                  $product_images[$key] =  static::rmv_prefix($value);
                }
                $prefixed_array = preg_filter('/^/', url('public/uploads/products') . '/m_', $product_images);
                $transformer->images = $prefixed_array;
            }else{
                $transformer->images = $prefixed_array;
            }
           

            //$prefixed_array = preg_filter('/^/', url('public/uploads/products') . '/', json_decode($item->images));
            //$transformer->images = $prefixed_array;

            $store = new \stdClass();
            $store->id = $item->store_id;
            $store->name = $item->store_name;
            $store->image = url('public/uploads/stores').'/'.$item->store_image;
            $store->rate = $item->store_rate;
            $store->available = $item->store_available;
            $store->available_text = $item->store_available == 0 ? _lang('app.closed') : _lang('app.opened');

            if ($user) {
                if ($user->type == 1) {
                    $transformer->is_favourite = $item->is_favourite ? 1 : 0;
                    $transformer->store = $store;
                }else if($user->type == 2){
                   $transformer->category = $item->category;
                   $transformer->category_id = $item->category_id;
                   $transformer->has_offer = $item->has_offer;
               }
           }else{
               $transformer->is_favourite = 0;
               $transformer->store = $store;

           }
        return $transformer;
   }

protected static function boot() {
    parent::boot();

    static::deleting(function($product) {

    });

    static::deleted(function($product) {

        $product_images = json_decode($product->images);
        foreach ($product_images as $image) {
            Product::deleteUploaded('products', $image);
        }

    });
}
}
