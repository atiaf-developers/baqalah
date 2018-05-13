<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends MyModel {

    protected $table = "cart";

    public static function transform($item)
    {
        $transformer = new \stdClass();

        $transformer->id = $item->id;
        $transformer->name = $item->name;
        $transformer->price = $item->price;
        $transformer->quantity = $item->quantity;
        $prefixed_array = preg_filter('/^/', url('public/uploads/products') . '/', json_decode($item->images));
        $transformer->image = $prefixed_array[0];

        return $transformer;
    }
}
