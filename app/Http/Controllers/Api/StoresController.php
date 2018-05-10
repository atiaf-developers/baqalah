<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Validator;
use App\Models\Store;
use App\Models\Category;
use DB;

class StoresController extends ApiController {

    private $rules = array(
        'container_id' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

    public function index()
    {
        $user = $this->auth_user();
        $distance = 5;

            $columns = ['stores.id','stores.name','stores.description','stores.image','stores.phone','stores.lat','stores.lng','stores.address','stores.available'];

            $stores = Store::where('stores.id',$id);
            $stores->where('stores.active',true);
            
            $stores->leftJoin('ratings', function ($join) use($user,$id) {
                    $join->on('ratings.store_id', '=', 'stores.id');
                    $join->where('ratings.user_id',  $user->id);
                    $join->where('ratings.store_id', $id);    
               
                $columns[]="ratings.id as is_rated";
                $columns[] = DB::raw("(SELECT Count(*) FROM products WHERE store_id = {$id} and active = 1) as number_of_products");
                $columns[] = 'stores.rate';
            }
            $stores->select($columns);
            $stores = $stores->paginate($this->limit);

            return _api_json(Store::transformCollection($stores));
    }

    public function show(Request $request,$id) {
        try {

            $user = $this->auth_user();

            $columns = ['stores.id','stores.name','stores.description','stores.image','stores.phone','stores.lat','stores.lng','stores.address','stores.available'];

            $store = Store::where('stores.id',$id);
            $store->where('stores.active',true);
            if ($user->type == 1) {
                $store->leftJoin('ratings', function ($join) use($user,$id) {
                    $join->on('ratings.store_id', '=', 'stores.id');
                    $join->where('ratings.user_id',  $user->id);
                    $join->where('ratings.store_id', $id);    
                });
                $columns[]="ratings.id as is_rated";
                $columns[] = DB::raw("(SELECT Count(*) FROM products WHERE store_id = {$id} and active = 1) as number_of_products");
                $columns[] = 'stores.rate';
            }
            $store->select($columns);
            $store = $store->first();

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

    private function getStores($store_id = null)
    {

    }



}
