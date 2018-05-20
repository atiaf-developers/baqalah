<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Device;

class LoginController extends ApiController {

    private $rules = array(
        'username' => 'required',
        'password' => 'required',
        'type' => 'required',
        'device_id' => 'required',
        'device_token' => 'required',
        'device_type' => 'required',
    );

    public function login(Request $request) {

        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json(new \stdClass(), ['errors' => $errors], 400);
        } else {
            $credentials = $request->only('username', 'password', 'type');
            if ($user = $this->auth_check($credentials)) {
                $token = new \stdClass();
                $token->id = $user->id;
                $token->device_id = $request->input('device_id');
                $token->expire = strtotime('+' . $this->expire_no . $this->expire_type);
                $expire_in_seconds = $token->expire;
                Device::updateOrCreate(
                        ['device_id' => $request->input('device_id'), 'user_id' => $user->id], ['device_token' => $request->input('device_token'), 'device_type' => $request->input('device_type')]
                );

                $user = User::transform($user);
                return _api_json($user, ['message' => _lang('app.login_done_successfully'), 'token' => AUTHORIZATION::generateToken($token), 'expire' => $expire_in_seconds]);
            }
            return _api_json(new \stdClass(), ['message' => _lang('app.invalid_credentials')], 400);
        }
    }

    private function auth_check($credentials) {
        $find = User::where('username', $credentials['username'])
                ->where('type', $credentials['type'])
                ->where('active', 1)
                ->first();
        if ($find) {
            if (password_verify($credentials['password'], $find->password)) {
                return $find;
            }
        }
        return false;
    }

}
