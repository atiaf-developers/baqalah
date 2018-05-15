<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends MyModel
{
    protected $table = "orders";
    private static $delivery_types = [
    	1 => 'delivery_by_store',
    	2 => 'receiving_the_order'
    ];

    private static $status = [
    	0 => '',
    	1 => '',
    	2 => '',
    	3 => '',
    	4 => ''
    ];

	public function order_detailes()
	{
		return $this->hasMany(OrderDetails::class,'order_id');
	}
	public static function transform($item)
	{
		$transformer = new \stdClass();
		$transformer->id = $item->id;
		$transformer->delivery_type = $item->delivery_type;
		$transformer->delivery_type_text = _lang('app.'.static::$delivery_types[$item->delivery_type]);
		$transformer->date = date('h:i A Y/m/d',$item->date);
		$transformer->status = $item->status;
		$transformer->status_text = _lang('app.'.static::$status[$item->status]);
        
        $delivery_method = new \stdClass();
        $delivery_method->delivery_type = $item->delivery_type;
        $delivery_method->delivery_type_text = _lang('app.'.static::$delivery_types[$item->delivery_type]);
        $delivery_method->name = $item->name;
        $delivery_method->mobile = $item->mobile;
		if ($item->delivery_type == 1) {
			$delivery_method->lat = $item->lat;
			$delivery_method->lng = $item->lng;
			$delivery_method->address = $item->address;
			$delivery_method->building = $item->building;
			$delivery_method->floor = $item->floor;
		}
		$transformer->delivery = $delivery_method;

        if ($item->store_id) {
	        $transformer->total_price = $item->total_price;
			$store = new \stdClass();
			$store->id = $item->store_id;
			$store->name = $item->store_name;
			$store->image = url('public/uploads/stores').'/'.$item->store_image;

			$transformer->store = $store;
        }else{
           $user = new \stdClass();
			$user->name = $item->fname . ' ' .$item->lname;
			$user->image = url('public/uploads/users').'/'.$item->image;
			$user->mobile = $item->mobile;

			$transformer->user = $user;
        }
		return $transformer;
		
	}



	protected static function boot() {
		parent::boot();

		static::deleting(function($video) {
			
		});

	
	}


}
