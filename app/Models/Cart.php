<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Cart extends MyModel {

    protected $table = "cart";

    public static function getCartApi($user_id) {
        $cart = static::join('products', 'cart.product_id', '=', 'products.id')
                ->join('stores', 'cart.store_id', '=', 'stores.id')
                ->join('users', 'stores.user_id', '=', 'users.id')
                ->where('cart.user_id', $user_id)
                ->select("cart.id", 'products.id as product_id', 'products.price', 'products.name', 'products.images', 'cart.quantity', 'products.quantity as product_quantity', 'products.store_id', DB::raw('(products.price * cart.quantity) as total_price'), 'users.id as user_id', 'users.device_type', 'users.device_token','stores.orders_notify')
                ->get();
        return $cart;
    }

    public static function checkAvailiabilty($user_id) {

        $errors = array();
        $cart = Cart::getCartApi($user_id);
        if ($cart->count() > 0) {
            foreach ($cart as $product) {
                if ($product->quantity > $product->product_quantity) {

                    if ($product->product_quantity == 0) {
                        $message = _lang('app.is_out_of_stock');
                    } else {
                        $message = _lang('app.available_quantity_is') . ' ' . $product->product_quantity;
                    }
                    $errors[] = $product->name . ' ' . $message;
                }
            }
        }
        return $errors;
    }

    public static function transform($item) {
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
