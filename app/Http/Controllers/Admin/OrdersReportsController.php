<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BackendController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Pagination\LengthAwarePaginator;
use App;
use Auth;
use DB;
use Redirect;
use Validator;
use App\Models\User;
use App\Models\Order;
use App\Models\Store;
use Config;

class OrdersReportsController extends BackendController {

    private $limit = 10;

    public function __construct() {


        parent::__construct();
        $this->middleware('CheckPermission:orders_reports,open', ['only' => ['index']]);
    }

    public function index(Request $request) {

        if ($request->all()) {
            foreach ($request->all() as $key => $value) {
                if($key=='status'&&$request->input('delivery_type')==1){
                    $key='status_one';
                }
                if($key=='status'&&$request->input('delivery_type')==2){
                    $key='status_two';
                }
                if ($value) {
                    
                    $this->data[$key] = $value;
                }
            }
        }
        $this->data['orders'] = Order::getOrdersAdmin($request);
        $this->data['info'] = Order::getInfoAdmin($request);
        $this->data['stores'] = Store::all();
        $this->data['users'] = $this->getUsers();
        $this->data['status_one_arr'] = Order::$status_one;
        $this->data['status_two_arr'] = Order::$status_two;
        return $this->_view('orders_reports.index', 'backend');
    }

    public function show(Request $request,$id) {
        $order = Order::getOrdersAdmin($request,$id);
        if (!$order) {
            return $this->err404();
        }
        $order->details=Order::getOrderDetailsAdmin($id);
        $this->data['order'] = $order;
        $this->data['status_one'] = Order::$status_one;
        $this->data['status_two'] = Order::$status_two;
        //dd($this->data['order']);
        return $this->_view('orders_reports/view', 'backend');
    }
    
     public function reply(Request $request) {

        try {

            $order_id = decrypt($request->input('order_id'));


            $order = Order::find($order_id);
            if ($order) {

                $order->reply = $request->input('reply');
                $order->save();
          
                return _json('success', _lang('app.updated_successfully'));
            } else {
                return _json('error', _lang('app.order_not_found'),400);
            }
        } catch (DecryptException $e) {
            return _json('error', _lang('app.error_is_occured'),400);
        } catch (\Exception $e) {
            return _json('error', _lang('app.error_is_occured'),400);
        }
    }

    

    

    private function getUsers() {
        $Users = User::select('id', "username")->where('type',1)->get();
        return $Users;
    }

    

}
