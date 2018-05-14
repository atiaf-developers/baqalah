<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Order;
use App\Models\Cart;
use DB;

class OrdersController extends ApiController {
    
    private $delivery_rules = array(
    	'lat' => 'required',
    	'lng' => 'required',
    	'name' => 'required',
    	'mobile' => 'required',
    	'building' => 'required',
    	'floor' => 'required',
    );

    private $recieve_rules = array(
    	'name' => 'required',
    	'mobile' => 'required',
    );

	public function __construct() {
		parent::__construct();
	}

	public function store(Request $request)
	{
		if ($request->delivery_type == 1) {
			$rules = $delivery_rules;
		}else if($request->delivery_type == 2){
			$rules = $recieve_rules; 
		}
		else{
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
		}
		$validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 400);
        }
        try{
		$user = $this->auth_user();
		$products = Cart::join('products','cart.product_id','=','products.id')
		->join('stores','cart.store_id','=','stores.id')
		->where('cart.user_id',$user->id)
		->select('products.id','products.price','cart.quantity','products.store_id',DB::raw('(products.price * cart.quantity) as total_price'))
		->get();
        
		$orders = array();
		$stores = array();

		foreach ($products as $product) {
			if (in_array($product->store_id,$stores)) {
				continue;
			}
			$stores[] = $product->store_id;
			$order = new \stdClass();
			$order->store_id = $product->store_id;
			$order->total_price = 0;
			$items = array();

			foreach ($products as $key => $item) {
				if ($item->store_id == $order->store_id) {
					$items[] = array(
						'product_id' => $item->id,
						'quantity' => $item->quantity,
						'price' => $item->price,
						'total_price' => $item->total_price,
					);
					$order->total_price += $item->total_price;
				}
			}
			$order->details = $items;
			$orders[] = $order;
		}
		dd($orders);

		} catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
	}

}
