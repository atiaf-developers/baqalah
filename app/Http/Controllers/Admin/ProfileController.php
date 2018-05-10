<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\Admin;
use App\Models\Resturant;
use App\Models\Group;

class ProfileController extends BackendController {

    private $rules = array(
        'username' => 'required',
        'password' => 'required',
        'email' => 'required|email',
        'phone' => 'required|numeric',
    );

    public function index() {
        if ($this->User->type == 2) {
            $resturant = Resturant::where('admin_id',$this->User->id)->first();
            $this->data['available'] = $resturant->available;
        }
        return $this->_view('profile/index', 'backend');
    }

    public function update(Request $request) {

        if ($request->input('password') === null) {
            unset($this->rules['password']);
        }
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return response()->json([
                        'type' => 'error',
                        'errors' => $errors
            ]);
        } else {
            $errors = $this->inputs_check('\App\Models\Admin', array(
                'username' => $request->input('username'),
                'email' => $request->input('email')
                    ), $this->User->id);
            if (!empty($errors)) {
                return response()->json([
                            'type' => 'error',
                            'errors' => $errors
                ]);
            }
            $this->User->username = $request->input('username');
            $this->User->email = $request->input('email');
            $this->User->phone = $request->input('phone');
            if ($request->input('password') !== null) {
                $this->User->password = bcrypt($request->input('password'));
            }
            if ($this->User->type == 2) {
                $resturant = Resturant::where('admin_id',$this->User->id)->first();
                $resturant->available = $request->available;
                $resturant->save();
            }
            try {
                $this->User->save();
                return _json('success', _lang('app.updated_successfully'));
            } catch (Exception $ex) {
                return _json('error', _lang('app.error_is_occured'));
            }
        }
    }

}
