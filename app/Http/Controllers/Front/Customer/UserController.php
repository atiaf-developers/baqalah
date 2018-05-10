<?php

namespace App\Http\Controllers\Front\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Noti;

class UserController extends FrontController {

    private $edit_rules = array(
    );

    public function __construct() {
        parent::__construct();
    }

    public function showEditForm() {
        $view = 'customer/edit';
        return $this->_view($view);
    }

    public function edit(Request $request) {
        $User = $this->User;
        if ($request->input('password')) {
            $this->edit_rules['password'] = "required";
            $this->edit_rules['confirm_password'] = "required|same:password";
        }
        if ($request->file('image')) {
            $this->edit_rules['image'] = "image|mimes:gif,png,jpeg|max:1000";
        }
        $rules['mobile'] = "required|unique:users,mobile,$User->id";
        $rules['username'] = "required|unique:users,username,$User->id";
        $rules['name'] = "required";
        $rules['email'] = "email";

        $validator = Validator::make($request->all(), $this->edit_rules);
        if ($validator->fails()) {
            $this->errors = $validator->errors()->toArray();
            if ($request->ajax()) {
                return _json('error', $this->errors);
            } else {
                return redirect()->back()->withInput($request->all())->withErrors($this->errors);
            }
        }

        try {
            $User->name = $request->input('name');
            $User->username = $request->input('username');
            $User->mobile = $request->input('mobile');
            if ($request->input('email')) {
                $User->email = $request->input('email');
            }
            if ($image = $request->input('password')) {
                $User->password = bcrypt($request->input('password'));
            }
            if ($image = $request->file('image')) {
                User::deleteUploaded('users', $User->image);

                $User->image = User::upload($image, 'users', true);
            }
            $User->save();
            $message = _lang('app.registered_done_successfully');
            if ($request->ajax()) {
                return _json('success', $message);
            } else {
                return redirect()->back()->withInput($request->all())->with(['successMessage' => $message]);
            }
        } catch (\Exception $ex) {
            dd($ex->getMessage());
            $message = _lang('app.error_is_occured');
            if ($request->ajax()) {
                return _json('error', $message);
            } else {
                return redirect()->back()->withInput($request->all())->with(['errorMessage' => $message]);
            }
        }
    }

    public function notifications() {
        $where_array['notifier_id'] = $this->User->id;
        $where_array['notifiable_type'] = 1;
        $this->data['noti'] = Noti::getNoti($where_array,'ForFront');
        //dd($this->data['noti']);
        $view = 'customer.notifications';
        return $this->_view($view);
    }

}
