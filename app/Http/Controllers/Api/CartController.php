<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Cart;
use App\Models\Store;
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

            $cart = Cart::getCartApi($user->id);
            $total_price = $cart->sum(function ($product) {
                return $product->price * $product->quantity;
            });

            return _api_json(Cart::transformCollection($cart), ['total_price' => $total_price]);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

    public function check_availiabilty() {
        try {

            $user = $this->auth_user();

            $errors = Cart::checkAvailiabilty($user->id);
            if (!empty($errors)) {
                return _api_json('', ['message' => implode('\n', $errors)], 400);
            } else {
                return _api_json('');
            }
            return $errors;
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

            $store = Store::find($request->input('store_id'));
            if (!$store) {
                return _api_json('', ['message' => _lang('app.not_found')], 404);
            }
            if ($store->available == 0) {
                return _api_json('', ['message' => _lang('app.this_store_is_currently_closed')], 400);
            }

            $product = Product::where('id', $request->input('product_id'))
                    ->where('store_id', $request->input('store_id'))
                    ->where('active', true)
                    ->first();

            if (!$product) {
                return _api_json('', ['message' => _lang('app.not_found')], 404);
            } else if ($product->quantity == 0) {
                return _api_json('', ['message' => _lang('app.this_product_is_out_of_stock')], 400);
            }
            $user = $this->auth_user();
            $cart_item = Cart::where('product_id', $request->input('product_id'))
                    ->where('user_id', $user->id)
                    ->first();
            if ($cart_item) {
                $cart_item->quantity += 1;
                if ( $cart_item->quantity > $product->quantity ) {
                   return _api_json('', ['message' => $product->name . ' ' ._lang('app.available_quantity_is') . ' ' . $product->quantity], 400);
                }
                $cart_item->save();
            } else {
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

    public function update(Request $request, $id) {
        try {

            $validator = Validator::make($request->all(), $this->edit_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            }
            $user = $this->auth_user();

            $cart_item = Cart::join('products', 'products.id', 'cart.product_id')
                    ->where('cart.id', $id)
                    ->where('cart.user_id', $user->id)
                    ->select('cart.id', 'cart.quantity', 'products.name','products.quantity as product_quantity')
                    ->first();

            if (!$cart_item) {
                return _api_json('', ['message' => _lang('app.not_found')], 400);
            } else if ($request->input('quantity') > $cart_item->product_quantity) {
                return _api_json('', ['message' => $cart_item->name . ' ' ._lang('app.available_quantity_is') . ' ' . $cart_item->product_quantity], 400);
            }
            
            if ( $request->input('quantity') > $cart_item->product_quantity ) {
              
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

    public function destroy($id) {
        try {
            $user = $this->auth_user();
            $cart_item = Cart::where('id', $id)
                    ->where('user_id', $user->id)
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
