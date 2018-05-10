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

    public function getStore() {
        try {
            $user = $this->auth_user();
            $store = Store::where('user_id',$user->id)->where('active',true)->first();
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



}
