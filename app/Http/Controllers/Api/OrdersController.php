<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\ReceiptDetails;
use App\Models\Cart;
use App\Models\Product;
use App\Helpers\Fcm;
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
    private $status_rules = array(
        'order_id' => 'required',
        'status' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {
        try {
            $orders = $this->getOrders($request);
            return _api_json($orders);
        } catch (\Exception $e) {
            dd($e);
            $message = _lang('app.error_is_occured');
            return _api_json([], ['message' => $message], 400);
        }
    }

    public function store(Request $request) {
        if ($request->delivery_type == 1) {
            $rules = $this->delivery_rules;
        } else if ($request->delivery_type == 2) {
            $rules = $this->recieve_rules;
        } else {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 400);
        }
        DB::beginTransaction();
        try {
            $user = $this->auth_user();
            $errors = Cart::checkAvailiabilty($user->id);
            if (!empty($errors)) {
                return _api_json('', ['message' => implode('\n', $errors)], 400);
            }
            $cart = Cart::getCartApi($user->id);

            $settings = $this->settings();
            $commission = $settings['commission']->value;



            $receipt_detailes = new ReceiptDetails;
            $receipt_detailes->name = $request->input('name');
            $receipt_detailes->mobile = $request->input('mobile');
            if ($request->delivery_type == 1) {
                $receipt_detailes->lat = $request->input('lat');
                $receipt_detailes->lng = $request->input('lng');
                $receipt_detailes->address = getAddress($request->input('lat'), $request->input('lng'), $lang = "AR");
                $receipt_detailes->building = $request->input('building');
                $receipt_detailes->floor = $request->input('floor');
            }
            $receipt_detailes->save();

            $stores = array();
            $products_updated_quantity = array();
            $stores_user_id = array();
            $errors = array();
            if ($cart->count() > 0) {
                foreach ($cart as $product) {
                    $stores[$product->store_id][] = $product;
                    if (!in_array($product->user_id, $stores_user_id)) {
                        $stores_user_id[] = $product->user_id;
                    }
                }
            }
            //dd($stores_user_id);
            if (!empty($stores)) {
                foreach ($stores as $store => $products) {
                    $order = new Order;
                    $order->receipt_details_id = $receipt_detailes->id;
                    $order->delivery_type = $request->input('delivery_type');
                    $order->store_id = $product->store_id;
                    $order->user_id = $user->id;
                    $order->total_price = 0;
                    $order->date = date('Y-m-d');
                    $order->status = 0;
                    $order->save();
                    $items = array();
                    foreach ($products as $item) {
                        $items[] = array(
                            'order_id' => $order->id,
                            'product_id' => $item->product_id,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'total_price' => $item->total_price,
                        );
                        $order->total_price += $item->total_price;
                        $available_quantity = $item->product_quantity - $item->quantity;
                        $products_updated_quantity['quantity'][] = ['id' => $item->product_id, 'value' => $available_quantity];
                    }
                    $order->commission = $commission;
                    $order->commission_cost = ($order->total_price * $commission) / 100;
                    $order->save();
                    OrderDetails::insert($items);
                    //dd($products_updated_quantity);
                    $this->updateValues('App\Models\Product', $products_updated_quantity);
                }
            }
            Cart::where('user_id', $user->id)->delete();
            DB::commit();

            $notification['body'] = _lang('app.new_order');
            $notification['type'] = 1;
            $this->send_noti_fcm($notification, $stores_user_id);
            return _api_json('', ['message' => _lang('app.order_has_been_sent_successfully')]);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

    public function store2(Request $request) {
        if ($request->delivery_type == 1) {
            $rules = $this->delivery_rules;
        } else if ($request->delivery_type == 2) {
            $rules = $this->recieve_rules;
        } else {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 400);
        }
        DB::beginTransaction();
        try {

            $user = $this->auth_user();
            $products = Cart::join('products', 'cart.product_id', '=', 'products.id')
                    ->join('stores', 'cart.store_id', '=', 'stores.id')
                    ->join('users', 'stores.user_id', '=', 'users.id')
                    ->where('cart.user_id', $user->id)
                    ->select('products.id', 'products.price', 'cart.quantity', 'products.store_id', DB::raw('(products.price * cart.quantity) as total_price'), 'users.device_type', 'users.device_token')
                    ->get();

            $settings = $this->settings();
            $commission = $settings['commission']->value;



            $receipt_detailes = new ReceiptDetails;
            $receipt_detailes->name = $request->input('name');
            $receipt_detailes->mobile = $request->input('mobile');
            if ($request->delivery_type == 1) {
                $receipt_detailes->lat = $request->input('lat');
                $receipt_detailes->lng = $request->input('lng');
                $receipt_detailes->address = getAddress($request->input('lat'), $request->input('lng'), $lang = "AR");
                $receipt_detailes->building = $request->input('building');
                $receipt_detailes->floor = $request->input('floor');
            }
            $receipt_detailes->save();

            $stores = array();
            $tokens_and = array();
            $tokens_ios = array();
            foreach ($products as $product) {
                if (in_array($product->store_id, $stores)) {
                    continue;
                }
                $stores[] = $product->store_id;

                $order = new Order;
                $order->receipt_details_id = $receipt_detailes->id;
                $order->delivery_type = $request->input('delivery_type');
                $order->store_id = $product->store_id;
                $order->user_id = $user->id;
                $order->total_price = 0;
                $order->date = date('Y-m-d H:i:s');
                $order->status = 0;
                $order->save();

                $items = array();
                foreach ($products as $key => $item) {
                    if ($item->store_id == $order->store_id) {
                        $items[] = array(
                            'order_id' => $order->id,
                            'product_id' => $item->id,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'total_price' => $item->total_price,
                        );
                        $order->total_price += $item->total_price;
                        Product::where('id', $item->id)->decrement('quantity', $item->quantity);
                    }
                }

                $order->commission = $commission;
                $order->commission_cost = ($order->total_price * $commission) / 100;
                $order->save();
                OrderDetails::insert($items);

                if ($product->device_type == 1) {
                    $tokens_and[] = $product->device_token;
                } else {
                    $tokens_ios[] = $product->device_token;
                }
            }

            Cart::where('user_id', $user->id)->delete();
            DB::commit();




            $Fcm = new Fcm;
            $notification['title'] = 'بقالة';
            $notification['body'] = 'طلب جديد';
            $notification['type'] = '1';
            $Fcm->send($tokens_and, $notification, 'and');
            $Fcm->send($tokens_ios, $notification, 'ios');
            return _api_json('', ['message' => _lang('app.order_has_been_sent_successfully')]);
        } catch (\Exception $e) {
            DB::rollback();
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

    public function status(Request $request) {

        $validator = Validator::make($request->all(), $this->status_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        }

        DB::beginTransaction();
        try {
            $user = $this->auth_user();
            $order = Order::join('stores', 'orders.store_id', '=', 'stores.id')
                    ->join('users', 'orders.user_id', '=', 'users.id')
                    ->where('orders.id', $request->input('order_id'))
                    ->where('stores.user_id', $user->id)
                    ->select('orders.*')
                    ->first();

            if (!$order) {
                return _api_json('', ['message' => _lang('app.not_found')], 404);
            }
            $order->status = $request->input('status');
            $order->save();

            if ($order->status == 1) {
                $order_products = Order::Join('order_details', 'orders.id', '=', 'order_details.order_id')
                        ->join('products', 'order_details.product_id', '=', 'products.id')
                        ->where('orders.id', $request->input('order_id'))
                        ->select('order_details.product_id', 'order_details.quantity', 'products.quantity as product_quantity')
                        ->get();

                foreach ($order_products as $item) {
                    $available_quantity = $item->product_quantity + $item->quantity;
                    $products_updated_quantity['quantity'][] = ['id' => $item->product_id, 'value' => $available_quantity];
                }
                //dd($products_updated_quantity);

                $this->updateValues('App\Models\Product', $products_updated_quantity);
            }

            DB::commit();

            $status = $order->delivery_type == 1 ? Order::$status_one : Order::$status_two;
            $notification['body'] = isset($status[$order->status]) ? _lang('app.' . $status[$order->status]['client']) : '';
            $notification['type'] = 1;
            $this->send_noti_fcm($notification, [$order->user_id]);
            return _api_json('', ['message' => _lang('app.updated_successfully')]);
        } catch (\Exception $e) {
            DB::rollback();
            //dd($e);
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

    private function getOrders($request) {
        $user = $this->auth_user();
        $status = array();

        if ($user->type == 1) {
            $status = Order::$user_status['client']['current'];
            $transformer = 'Client';
        } else {
            $status = Order::$user_status['client']['current'];
            $transformer = 'Store';
            if ($request->input('type') == 1) {
                $status = Order::$user_status['store']['current'];
            } else if ($request->input('type') == 2) {
                $status = Order::$user_status['store']['waiting'];
            } else {
                $status = Order::$user_status['store']['previous'];
            }
        }
        //dd($user->type);
        $columns = ['orders.id', 'orders.delivery_type', 'orders.date', 'orders.status', 'receipt_details.name', 'receipt_details.mobile', 'receipt_details.lat', 'receipt_details.lng', 'receipt_details.building', 'receipt_details.floor', 'receipt_details.address'];
        $orders = Order::join('receipt_details', 'orders.receipt_details_id', '=', 'receipt_details.id');
        if ($user->type == 1) {
            $orders->join('stores', 'orders.store_id', '=', 'stores.id');
            $orders->where('orders.user_id', $user->id);
            $columns[] = "orders.total_price";
            $columns[] = "stores.id as store_id";
            $columns[] = "stores.name as store_name";
            $columns[] = "stores.image as store_image";
        } else {
            $orders->join('users', 'orders.user_id', '=', 'users.id');
            $orders->where('orders.store_id', $request->input('store_id'));
            $columns[] = "users.fname";
            $columns[] = "users.lname";
            $columns[] = "users.image";
            $columns[] = "users.mobile";
        }

        $orders->whereIn('status', $status);
        $orders->select($columns);
        //dd($orders->toSql());
        $orders = $orders->paginate($this->limit);
        return Order::transformCollection($orders, $transformer);
    }

}
