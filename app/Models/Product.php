<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends MyModel
{
    protected $table = "products";


    public static function transform($item)
    {
        $user = static::auth_user();
    	$transformer = new \stdClass();

    	$transformer->id = $item->id;
    	$transformer->name = $item->name;
    	$transformer->description = $item->description;
    	$transformer->price = $item->price;
    	$transformer->quantity = $item->quantity;
        $transformer->category = $item->category;
        $transformer->category_id = $item->category_id;
    	$transformer->has_offer = $item->has_offer;
        if ($user->type == 1) {
            $transformer->is_favourite = $item->is_favourite ? 1 : 0;
        }
        $prefixed_array = preg_filter('/^/', url('public/uploads/products') . '/', json_decode($item->images));
        $transformer->images = $prefixed_array;
        
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
