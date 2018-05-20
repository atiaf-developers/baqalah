<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Store;
use App\Models\StoreCtegory;
use App\Models\Device;
use DB;

class RegisterController extends ApiController {

    private $client_rules = array(
        'step' => 'required',
        'first_name' => 'required',
        'last_name' => 'required',
        'username' => 'required|unique:users',
        'email' => 'email|unique:users',
        'mobile' => 'required|unique:users',
        'password' => 'required',
        'gender' => 'required',
        'device_id' => 'required',
        'device_token' => 'required',
        'device_type' => 'required',
    );
    private $store_rules_step_one = array(
        'step' => 'required',
        'type' => 'required',
        'store_name' => 'required|unique:stores,name',
        'username' => 'required|unique:users',
        'email' => 'email|unique:users',
        'mobile' => 'required|unique:users',
        'password' => 'required',
    );
    private $store_rules_step_two = array(
        'step' => 'required',
        'type' => 'required',
        'store_image' => 'required',
        'store_description' => 'required',
        'store_categories' => 'required',
        'lat' => 'required',
        'lng' => 'required',
    );
    private $store_rules = array(
        'step' => 'required',
        'type' => 'required',
        'username' => 'required|unique:users',
        'email' => 'email|unique:users',
        'mobile' => 'required|unique:users',
        'password' => 'required',
        'device_id' => 'required',
        'device_token' => 'required',
        'device_type' => 'required',
        'store_name' => 'required|unique:stores,name',
        'store_image' => 'required',
        'store_description' => 'required',
        'store_categories' => 'required',
        'lat' => 'required',
        'lng' => 'required',
    );

    public function __construct() {
        parent::__construct();
    }

    public function register(Request $request) {

        if ($request->type == 1) {
            if ($request->step == 1 || $request->step == 2) {
                $rules = $this->client_rules;
            } else {
                return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
            }
        } else if ($request->type == 2) {

            if ($request->step == 1) {
                $rules = $this->store_rules_step_one;
            } else if ($request->step == 2) {
                $rules = $this->store_rules_step_two;
            } else if ($request->step == 3) {
                $rules = $this->store_rules;
            } else {
                return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
            }
        } else {
            return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        }

        if ($request->step == 1 && $request->type == 2) {
            return _api_json(new \stdClass());
        } else if (($request->step == 1 && $request->type == 1) || ($request->step == 2 && $request->type == 2)) {
            $verification_code = Random(4);
            return _api_json(new \stdClass(), ['code' => $verification_code]);
        } else if (($request->step == 2 && $request->type == 1) || ($request->step == 3 && $request->type == 2)) {

            DB::beginTransaction();
            try {
                $user = $this->createUser($request);
                DB::commit();

                $token = new \stdClass();
                $token->id = $user->id;
                $token->expire = strtotime('+' . $this->expire_no . $this->expire_type);
                $token->device_id = $request->input('device_id');
                $expire_in_seconds = $token->expire;
                return _api_json(User::transform($user), ['token' => AUTHORIZATION::generateToken($token), 'expire' => $expire_in_seconds], 201);
            } catch (\Exception $e) {
                DB::rollback();
                $message = _lang('app.error_is_occured');
                return _api_json(new \stdClass(), ['message' => $e->getMessage()], 400);
            }
        } else {
            return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    private function createUser($request) {

        $User = new User;

        $settings = $this->settings();
        $activation_type = $settings['stores_activation']->value;

        $User->username = $request->input('username');
        $User->email = $request->input('email');
        $User->password = bcrypt($request->input('password'));
        if ($request->type == 1) {
            $User->fname = $request->input('first_name');
            $User->lname = $request->input('last_name');
            $User->gender = $request->input('gender');
            $User->mobile = $request->input('mobile');
        }
        $User->type = $request->type;
        $User->image = "default.png";

        if ($request->type == 1) {
            $User->active = 1;
        } else {
            $User->active = $activation_type == 1 ? 0 : 1;
        }

        $User->device_type = $request->device_type;
        $User->device_token = $request->device_token;
        $User->save();
        $Device = new Device;
        $Device->device_id = $request->input('device_id');
        $Device->device_token = $request->input('device_token');
        $Device->device_type = $request->input('device_type');
        $Device->user_id = $User->id;
        $Device->save();
        
        if ($request->type == 2) {
            $this->createStore($request, $User, $activation_type);
        }
        return $User;
    }

    private function createStore($request, $User, $activation_type) {

        $store = new Store;
        $store->name = $request->input('store_name');
        $store->description = $request->input('store_description');
        $store->image = Store::upload($request->input('store_image'), 'stores', true, false, true);
        $store->lat = $request->input('lat');
        $store->lng = $request->input('lng');
        $store->address = getAddress($request->input('lat'), $request->input('lng'), $lang = "AR");
        $store->active = $activation_type == 1 ? 0 : 1;
        $store->available = 1;
        $store->mobile = $request->input('mobile');
        $store->user_id = $User->id;

        $store->save();

        $store_categories = array();
        $categories = json_decode($request->input('store_categories'));
        foreach ($categories as $value) {
            $store_categories[] = array(
                'store_id' => $store->id,
                'category_id' => $value
            );
        }
        StoreCtegory::insert($store_categories);
    }

}
