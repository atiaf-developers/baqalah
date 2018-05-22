<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BackendController;
use Illuminate\Http\Request;
//use App\Helpers\RandomColor;
use App\Models\User;
use App\Models\Order;
//use App\Http\Middleware\CheckPermission;
//use App\Lib\Permissions;
use DB;
use Auth;

class AdminController extends BackendController {

    public function __construct() {

        parent::__construct();
    }

    public function index() {
        $this->data['counts'] = $this->getCounts();
//         $this->data['colors'] = RandomColor::many(count($this->data['counts']), array(
//                    'hue' => 'blue'
//        ));
        //dd($this->data['counts']);
        return $this->_view('index', 'backend');
    }

    public function error() {
        return view('main_content/backend/err404');
    }

    private function getCounts() {
        $clients = User::where('type',1)->select(DB::RAW('"clients" as type'), DB::RAW('COUNT(*) as count'));
        $stores = User::where('type',2)->select(DB::RAW('"stores" as type'), DB::RAW('COUNT(*) as count'));
        $result =  Order::whereNotIn('status',[1,4] )
                ->select(DB::RAW('"orders_in_proccessing" as type'), DB::RAW('COUNT(*) as count'))
                ->union($clients)
                ->union($stores)
                ->get()
                ->keyBy('type')
                ->toArray();
        return $result;

//        $sql = 'SELECT
//              (SELECT COUNT(*) FROM clients) as clientsCount, 
//              (SELECT COUNT(*) FROM designers) as designersCount';
//        $query = DB::select(DB::raw($sql));
//        return collect($query)->first();
    }

}
