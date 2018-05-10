<?php

namespace App\Http\Controllers\Front\Auth;

use App\Http\Controllers\FrontController;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Auth;
use Validator;

class LoginController extends FrontController {

    use AuthenticatesUsers;

    private $rules = array(
        'username' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('guest', ['except' => ['logout']]);
    }

    public function showLoginForm() {
        return $this->_view('auth/login');
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            if ($request->ajax()) {
                $errors = $validator->errors()->toArray();
                return response()->json([
                            'type' => 'error',
                            'errors' => $errors
                ]);
            } else {
                //dd($validator);
                return redirect()->back()->withInput($request->only('email'))->withErrors($validator->errors()->toArray());
            }
        } else {
            $username = $request->input('username');
            $password = $request->input('password');
            $User = $this->checkAuth($username);
            $is_logged_in = false;
            if ($User) {
                $is_logged_in = true;
                if ($password) {
                    if (password_verify($password, $User->password)) {
                        $is_logged_in = true;
                    } else {
                        $is_logged_in = false;
                    }
                }
            }
            if ($is_logged_in) {
                Auth::guard('web')->login($User);
                if ($request->ajax()) {

                    return _json('success', route('home'));
                } else {
                    return redirect()->intended(route('home'));
                }
            } else {
                $msg = _lang('messages.invalid_credentials');
                if ($request->ajax()) {
                    return _json('error', $msg);
                } else {
                    return redirect()->back()->withInput($request->only('email', 'remember'))->withErrors(['msg' => $msg]);
                }
            }
        }
    }

    public function logout() {
        Auth::guard('web')->logout();
        return redirect('/login');
    }

    private function checkAuth($username) {
        $user = User::where('username', $username)->first();
        //dd($user);
        return $user;
    }

}
