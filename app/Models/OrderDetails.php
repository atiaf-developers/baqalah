<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends MyModel
{
    protected $table = 'order_details';

    public static function transform($item)
    {
    	$transformer = new \stdClass();
    	$transformer->name = $item->name;
    	$prefixed_array = preg_filter('/^/', url('public/uploads/products') . '/', json_decode($item->images));
    	$transformer->image = $prefixed_array[0];
    	$transformer->price = $item->price;
    	$transformer->quantity = $item->quantity;
        
        return $transformer;

    }
}
