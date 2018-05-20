<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends MyModel {

    protected $table = "orders";
    private static $delivery_types = [
        1 => 'delivery_by_store',
        2 => 'receiving_the_order'
    ];
    public static $filter_status = [
        0 => 'قيد الانتظار',
        1 => 'تم رفض الطلب',
        2 => 'جارى تجهيز الطلب',
        3 => 'جارى توصيل الطلب',
        4 => 'تم توصيل الطلب'
    ];
    public static $status_one = array(
        0 => ['client' => 'waiting_for_reply', 'store' => 'purchase_request', 'admin' => 'client_sent_new_order_and_waiting_for_store_reply'],
        1 => ['client' => 'rejected', 'store' => 'rejected', 'admin' => 'rejected'], //المتجر رفض الطلب
        2 => ['client' => 'order_is_being_processed', 'store' => 'order_is_being_processed', 'admin' => 'order_is_being_processed'],
        3 => ['client' => 'your_order_on_the_way', 'store' => 'order_on_the_way', 'admin' => 'order_on_the_way'],
        4 => ['client' => 'your_order_has_been_delivered', 'store' => 'order_has_been_delivered', 'admin' => 'order_has_been_delivered'],
    );
    public static $status_two = array(
        0 => ['client' => 'waiting_for_reply', 'store' => 'purchase_request', 'admin' => 'client_sent_new_order_and_waiting_for_store_reply'],
        1 => ['client' => 'rejected', 'store' => 'rejected', 'admin' => 'rejected'], //المتجر رفض الطلب
        2 => ['client' => 'order_is_being_processed', 'store' => 'order_is_being_processed', 'admin' => 'order_is_being_processed'],
        3 => ['client' => 'order_is_processed', 'store' => 'order_is_processed', 'admin' => 'order_is_processed'],
        4 => ['client' => 'your_order_has_been_delivered', 'store' => 'order_has_been_delivered', 'admin' => 'order_has_been_delivered'],
    );
    public static $user_status = array(
        'store' => array(
            'waiting' => [0],
            'current' => [2, 3],
            'previous' => [4]
        ),
        'client' => array(
            'current' => [0, 1, 2, 3, 4]
        ),
    );

    public function order_details() {
        return $this->hasMany(OrderDetails::class, 'order_id');
    }

    public static function transformClient($item) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->status = $item->status;
        
        $status = $item->delivery_type==1?static::$status_one:static::$status_two;
        $transformer->status_text = isset($status[$item->status])?_lang('app.' . $status[$item->status]['store']):'';
        $transformer->order_detailes = OrderDetails::transformCollection($item->order_details()
                                ->join('products', 'order_details.product_id', '=', 'products.id')
                                ->select('products.name', 'products.images', 'order_details.price', 'order_details.quantity')
                                ->get());
        $transformer->total_price = $item->total_price;


        $store = new \stdClass();
        $store->id = $item->store_id;
        $store->name = $item->store_name;
        $store->image = url('public/uploads/stores') . '/' . $item->store_image;
        $transformer->store = $store;

        $transformer->receipt_detailes = static::deliveryMethod($item);
        $transformer->date = date('A h:i Y/m/d', strtotime($item->date));


        return $transformer;
    }

    public static function transformStore($item) {

        $transformer = new \stdClass();

        $user = new \stdClass();
        $transformer->date = date('h:i A Y/m/d', strtotime($item->date));
        $transformer->status = $item->status;
         $status = $item->delivery_type==1?static::$status_one:static::$status_two;
        $transformer->status_text = isset($status[$item->status])?_lang('app.' . $status[$item->status]['store']):'';

        $user->name = $item->fname . ' ' . $item->lname;
        $user->image = url('public/uploads/users') . '/' . $item->image;
        $user->mobile = $item->mobile;
        $transformer->user = $user;

        $transformer->id = $item->id;

        $transformer->order_detailes = OrderDetails::transformCollection($item->order_details()
                                ->join('products', 'order_details.product_id', '=', 'products.id')
                                ->select('products.name', 'products.images', 'order_details.price', 'order_details.quantity')
                                ->get());

        $transformer->receipt_detailes = static::deliveryMethod($item);


        return $transformer;
    }

    private static function deliveryMethod($item) {
        $delivery_method = new \stdClass();
        $delivery_method->delivery_type = $item->delivery_type;
        $delivery_method->delivery_type_text = _lang('app.' . static::$delivery_types[$item->delivery_type]);
        $delivery_method->name = $item->name;
        $delivery_method->mobile = $item->mobile;

        $delivery_method->lat = $item->lat ? $item->lat : "";
        $delivery_method->lng = $item->lng ? $item->lng : "";
        $delivery_method->address = $item->address ? $item->address : "";
        $delivery_method->building = $item->building ? $item->building : "";
        $delivery_method->floor = $item->floor ? $item->floor : "";

        return $delivery_method;
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($video) {
            
        });
    }

}
