<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends MyModel {

    protected $table = 'order_details';
    protected $casts = array(
        'price' => 'double',
        'quantity' => 'integer',
    );

    public static function transform($item) {
        $transformer = new \stdClass();
        $transformer->name = $item->name;
        $prefixed_array = preg_filter('/^/', url('public/uploads/products') . '/', json_decode($item->images));
        $transformer->image = $prefixed_array[0];
        $transformer->price = $item->price;
        $transformer->quantity = $item->quantity;

        return $transformer;
    }

    public static function transformAdmin($item) {
        $transformer = new \stdClass();
        $images = json_decode($item->images);
        $transformer->name = $item->name;
        if (count($images) > 0) {
            $prefixed_array = preg_filter('/^/', url('public/uploads/products') . '/', json_decode($item->images));
            $transformer->image = $prefixed_array[0];
        } else {
            $transformer->image = url('public/uploads/products/default.png');
        }


        $transformer->price = $item->price;
        $transformer->quantity = $item->quantity;
        $transformer->total_price = $item->total_price;

        return $transformer;
    }

}
