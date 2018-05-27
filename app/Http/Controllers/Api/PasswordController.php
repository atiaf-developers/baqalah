<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Mail\ResetPasswordMail;
use Mail;

class PasswordController extends ApiController {

    private $reset_rules = array(
        'email' => 'required|email',
    );
    private $verify_rules = array(
        'email' => 'required|email',
        'password' => 'required',
        'confirm_password' => 'required|same:password',
    );

    public function __construct() {
        parent::__construct();
    }

    public function reset(Request $request) {
        $validator = Validator::make($request->all(), $this->reset_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors],400);
        } else {
            $User = User::where('email', $request->input('email'))
                    ->first();

            if ($User == null) {
                return _api_json('', ['message' => _lang('app.invalid_email')],400);
            } else {
                $verification_code = Random(4);
                Mail::to($request->input('email'))->send(new ResetPasswordMail($verification_code));

                return _api_json('', ['verification_code' =>  $verification_code]);
            }
        }
    }

    public function verify(Request $request) {
        $validator = Validator::make($request->all(), $this->verify_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors],400);
        } else {
            $User = User::where('email', $request->input('email'))->first();
            if ($User == null) {
                return _api_json( '', ['message' => _lang('app.invalid_email')],400);
            } else {
                try {
                    $User->password = bcrypt($request->input('password'));
                    $User->save();
                    return _api_json('', ['message' => _lang('app.updated_successfully')]);
                } catch (Exception $ex) {
                    return _api_json('', ['message' => _lang('error_is_occured')],400);
                }
            }
        }
    }

}
