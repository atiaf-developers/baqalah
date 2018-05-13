<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Cart;
use App\Models\Product;
use DB;

class CartController extends ApiController {

    
    private $add_rules = array(
        'product_id' => 'required',
        'store_id' => 'required',
    );

    private $edit_rules = array(
        'quantity' => 'required',
    );

    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {
        try {

            $user = $this->auth_user();

            $cart = Product::Join('cart', function ($join) use($user) {
                $join->on('cart.product_id', '=', 'products.id');
                $join->where('cart.user_id', $user->id);    
            }) 
            ->join('stores', 'stores.id', '=', 'products.store_id')
            ->select("cart.id",'products.name','products.images','cart.quantity',
                        'products.price')
            ->get();

            $total_price = $cart->sum(function ($product) {
                return $product->price * $product->quantity;
            });

            return _api_json(Cart::transformCollection($cart),['total_price' => $total_price]);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

    
    public function store(Request $request) {
       try {

        $validator = Validator::make($request->all(), $this->add_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 400);
        }
        $user = $this->auth_user();
        $cart_item = Cart::where('product_id',$request->input('product_id'))
                    ->where('user_id',$user->id)
                    ->first();
        if ($cart_item) {
            $cart_item->quantity += 1;
            $cart_item->save();
        }else{
            $cart_item = new Cart;
            $cart_item->product_id = $request->input('product_id');
            $cart_item->store_id = $request->input('store_id');
            $cart_item->quantity = 1;
            $cart_item->user_id = $user->id;
            $cart_item->save();
        }
        $message = _lang('app.added_successfully');
        return _api_json('', ['message' => $message]);
       } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
       }
       
    }

    public function update(Request $request,$id)
    {
        try {
    
            $validator = Validator::make($request->all(), $this->edit_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            }
            $user = $this->auth_user();

            $cart_item = Cart::where('id',$id)
                        ->where('user_id',$user->id)
                        ->first();

            if (!$cart_item) {
              return _api_json('', ['message' => _lang('app.not_found')], 400);
            }

            $cart_item->quantity = $request->input('quantity');
            $cart_item->save();
            
            $message = _lang('app.updated_successfully');
            return _api_json('', ['message' => $message]);
            
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }


    public function destroy($id)
    {
        try {
            $user = $this->auth_user();
            $cart_item = Cart::where('id',$id)
                        ->where('user_id',$user->id)
                        ->first();
            if (!$cart_item) {
              return _api_json('', ['message' => _lang('app.not_found')], 400);
            }
            $cart_item->delete();
            $message = _lang('app.deleted_successfully');
            return _api_json('', ['message' => $message]);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

   

    


}
