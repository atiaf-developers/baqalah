<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Validator;
use App\Models\Admin;
use App\Models\Group;

//use My;

class LoginController extends Controller {

    use AuthenticatesUsers;

    private $rules = array(
        'username' => 'required',
        'password' => 'required'
    );

    public function __construct() {
        $this->middleware('guest:admin', ['except' => ['logout']]);
    }

    public function showLoginForm() {

        return view('main_content/backend/login');
    }

    public function login(Request $request) {

        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            if ($request->ajax()) {
                $errors = $validator->errors()->toArray();
                return _json('error', $errors);
            } else {
                return redirect()->back()->withInput($request->only('username', 'remember'))->withErrors($validator->errors()->toArray());
            }
        } else {
            $username = $request->input('username');
            $password = $request->input('password');
            $Admin = $this->checkAuth($username);
            $is_logged_in = false;
            if ($Admin) {
                if (password_verify($password, $Admin->password)) {
                    $is_logged_in = true;
                }
            }
            if ($is_logged_in) {
                Auth::guard('admin')->login($Admin);
                if ($request->ajax()) {
                    return _json('success',  route('admin.dashboard'));
                } else {
                    return redirect()->intended( route('admin.dashboard'));
                }
            } else {
                $msg = _lang('messages.invalid_credentials');
                if ($request->ajax()) {
                    return _json('error', $msg);
                } else {
                    return redirect()->back()->withInput($request->only('username', 'remember'))->withErrors(['msg' => $msg]);
                }
            }
        }
    }


    private function checkAuth($username) {
       $Admin=Admin::join('groups','groups.id','=','admins.group_id')
               ->where('groups.active',1)
               ->where('admins.active',1)
               ->where('admins.username',$username)
               ->select('admins.*')
               ->first();
        if ($Admin) {
            return $Admin;
        }
        return false;
    }

    public function logout() {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    }

}
