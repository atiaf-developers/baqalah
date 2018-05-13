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

    private $rate_rules = array(
        'store_id' => 'required',
        'rate' => 'required'
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
        $distance = 10;
        $lat = $request->input('lat');
        $lng = $request->input('lng');

        $stores = Store::leftJoin('ratings', function ($join) use($user) {
            $join->on('ratings.store_id', '=', 'stores.id');
            $join->where('ratings.user_id',  $user->id);
        })
        ->where('stores.active',true)
        ->select(['stores.id','stores.name','stores.description','stores.image','stores.phone','stores.lat','stores.lng','stores.address','stores.available','ratings.id as is_rated',DB::raw("(SELECT Count(*) FROM products WHERE store_id = stores.id and active = 1) as number_of_products"),'stores.rate',DB::raw($this->iniDiffLocations('stores', $lat, $lng))])
        ->having('distance','<=',$distance)
        ->orderBy('distance')
        ->get();

        return _api_json(Store::transformCollection($stores));
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
            ->select(['stores.id','stores.name','stores.description','stores.image','stores.phone','stores.lat','stores.lng','stores.address','stores.available','ratings.id as is_rated',DB::raw("(SELECT Count(*) FROM products WHERE store_id = {$id} and active = 1) as number_of_products"),'stores.rate'])
            ->first();

            if (!$store) {
                return _api_json(new \stdClass(), ['message' => _lang('app.not_found')], 404);
            }
            return _api_json(Store::transform($store));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getStoreCategories(Request $request) {
        try {
            $user = $this->auth_user();

            $categories = Category::Join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
            ->join('store_categories', 'categories.id', '=', 'store_categories.category_id')
            ->join('stores', 'stores.id', '=', 'store_categories.store_id')
            ->where('categories_translations.locale', $this->lang_code)
            ->where('categories.active', true)
            ->where('stores.user_id', $user->id)
            ->orderBy('categories.this_order')
            ->select("categories.id", "categories_translations.title")
            ->get();

            return _api_json(Category::transformCollection($categories));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }


    public function rate(Request $request) {

        $validator = Validator::make($request->all(), $this->rate_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 400);
        }
        
        $user = $this->auth_user();
        $store = Store::find($request->input('store_id'));
        if (!$store) {
            $message = _lang('app.not_found');
            return _api_json('', ['message' => $message], 404);
        }

        $check = Rating::where('user_id',$user->id)
                        ->where('store_id',$request->input('store_id'))
                        ->first();
        if ($check) {
            return _api_json('', ['message' => _lang('app.you_have_already_rate_this_store')], 400);
        }
        DB::beginTransaction();
        try {
            
            $rate = new Rating;
            $rate->user_id = $user->id;
            $rate->store_id = $request->input('store_id');
            $rate->rate = $request->input('rate');
            $rate->save();

            $store_new_rate = Rating::where('store_id', $request->input('store_id'))
            ->select(DB::raw(' SUM(rate)/COUNT(*) as rate'))
            ->first();
            $store->rate = $store_new_rate->rate;
            $store->save();
            DB::commit();
            $message = _lang('app.rated_successfully');
            return _api_json('', ['message' => $message]);
        } catch (\Exception $e) {
            DB::rollback();
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

}
