<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Admin;
use App\Models\Group;
use Validator;

class AdminsController extends BackendController {

    private $rules = array(
        'username' => 'required',
        'password' => 'required',
        'email' => 'required|email',
        'phone' => 'required|numeric',
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:admins,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:admins,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:admins,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:admins,delete', ['only' => ['delete']]);
    }

    public function index() {
        $this->data['groups'] = Group::where('type',1)->get();
       //dd($this->data['groups']);
        return $this->_view('admins/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

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
                'email' => $request->input('email'),
            ));
            if (!empty($errors)) {
                return response()->json([
                            'type' => 'error',
                            'errors' => $errors
                ]);
            }

            $admin = new Admin;

            $admin->username = $request->input('username');
            $admin->email = $request->input('email');
            $admin->phone = $request->input('phone');
            $admin->password = bcrypt($request->input('password'));
            $admin->active = $request->input('active');
            $admin->group_id = $request->input('group_id');
            $admin->created_by = $this->User->id;
            if ($admin->save()) {
                return response()->json([
                            'type' => 'success',
                            'message' => _lang('app.added_successfully')
                ]);
            } else {
                return response()->json([
                            'type' => 'error',
                            'message' => _lang('app.error_is_occured')
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $find = Admin::find($id);

        if ($find) {
            return response()->json([
                        'type' => 'success',
                        'message' => $find
            ]);
        } else {
            return response()->json([
                        'type' => 'success',
                        'message' => 'error'
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        if ($request->input('password') === null) {
            unset($this->rules['password']);
        }
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json([
                        'type' => 'error',
                        'message' => _lang('app.error_is_occured')
                            ], 404);
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
                    ), $id);
            if (!empty($errors)) {
                return response()->json([
                            'type' => 'error',
                            'errors' => $errors
                ]);
            }
            $admin->username = $request->input('username');
            $admin->email = $request->input('email');
            $admin->phone = $request->input('phone');
            if ($request->input('password') !== null) {
                $admin->password = bcrypt($request->input('password'));
            }

            $admin->active = $request->input('active');
            $admin->group_id = $request->input('group_id');
            if ($admin->save()) {
                return response()->json([
                            'type' => 'success',
                            'message' => _lang('app.updated_successfully')
                ]);
            } else {
                return response()->json([
                            'type' => 'error',
                            'message' => _lang('app.error_is_occured')
                ]);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $admin = Admin::find($id);
        if (!$admin) {
            return response()->json([
                        'type' => 'error',
                        'message' => _lang('app.error_is_occured')
                            ], 404);
        }
        if ($admin->delete()) {

            return response()->json([
                        'type' => 'success',
                        'message' => _lang('app.deleted_successfully')
            ]);
        } else {
            return response()->json([
                        'type' => 'error',
                        'message' => _lang('app.error_is_occured')
            ]);
        }
    }

    public function data() {

        $admin = Admin::with('group')->where('admins.type', 1)->where('admins.id','!=', $this->User->id)->select('admins.*');

        return \Datatables::eloquent($admin)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('admins', 'edit') || \Permissions::check('admins', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('admins', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" onclick = "Admins.edit(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }


                                if (\Permissions::check('admins', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Admins.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }


                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->rawColumns(['options'])
                        ->make(true);
    }

}
