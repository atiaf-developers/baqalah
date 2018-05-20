<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Validator;
use App\Models\Store;
use App\Models\Category;
use App\Models\Rating;
use DB;

class StoresController extends ApiController {

    private $rules = array(
        'lat' => 'required',
        'lng' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 400);
        }
        $user = $this->auth_user();
        $settings = $this->settings();
        $distance = $settings['search_range_for_stores']->value;
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $stores = Store::where('stores.active',true)
        ->select(['stores.id','stores.name','stores.description','stores.image','stores.mobile','stores.lat','stores.lng','stores.address','stores.available',DB::raw("(SELECT Count(*) FROM products WHERE store_id = stores.id and active = 1) as number_of_products"),'stores.rate',DB::raw($this->iniDiffLocations('stores', $lat, $lng))])
        ->having('distance','<=',$distance)
        ->orderBy('distance')
        ->get();

        return _api_json(Store::transformCollection($stores,null,['user'=>$user]));
    }

    public function show(Request $request,$id) {
        try {

            $user = $this->auth_user();

            $store = Store::leftJoin('ratings', function ($join) use($user,$id) {
                $join->on('ratings.store_id', '=', 'stores.id');
                $join->where('ratings.user_id',  $user->id);
                $join->where('ratings.store_id', $id);  
            })
            ->where('stores.id',$id)
            ->where('stores.active',true)
            ->select(['stores.id','stores.name','stores.description','stores.image','stores.mobile','stores.lat','stores.lng','stores.address','stores.available','ratings.id as is_rated',DB::raw("(SELECT Count(*) FROM products WHERE store_id = {$id} and active = 1) as number_of_products"),'stores.rate'])
            ->first();

            if (!$store) {
                return _api_json(new \stdClass(), ['message' => _lang('app.not_found')], 404);
            }
            return _api_json(Store::transform($store));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

}
