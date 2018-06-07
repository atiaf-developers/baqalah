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
        $stores = $this->getStores($request);
        return _api_json($stores);
    }

    public function show(Request $request,$id) {
        try {

            $store = $this->getStores($request,$id);
            if (!$store) {
                return _api_json(new \stdClass(), ['message' => _lang('app.not_found')], 404);
            }
            return _api_json($store);
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }


    

}
