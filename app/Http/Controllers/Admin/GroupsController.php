<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Module;
use App\Models\Group;
use Validator;

class GroupsController extends BackendController {

    private $rules = array(
        'name' => 'required'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:groups,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:groups,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:groups,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:groups,delete', ['only' => ['delete']]);
    }

    public function index() {
        $this->data['modules'] = $this->getModules();
        return $this->_view('groups/index', 'backend');
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
            $group = new Group;

            $group->name = $request->input('name');
            $group->permissions = json_encode($request->input('group_options'));
            $group->active = $request->input('active');
            if ($group->save()) {
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
        $find = Group::find($id);
        $find->permissions = json_decode($find->permissions);
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
        $group = Group::find($id);
        if (!$group) {
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
            $group->name = $request->input('name');
            $group->permissions = json_encode($request->input('group_options'));
            $group->active = $request->input('active');
            if ($group->save()) {
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
        $group = Group::find($id);
        if (!$group) {
            return response()->json([
                        'type' => 'error',
                        'message' => _lang('app.error_is_occured')
                            ], 404);
        }
        if ($group->delete()) {

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
        $blog = Group::where('type', $this->User->type)->select(['id', 'name', 'active']);

        return \Datatables::eloquent($blog)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('groups', 'edit') || \Permissions::check('groups', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('groups', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" onclick = "Groups.edit(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }
                                if (\Permissions::check('groups', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Groups.delete(this);return false;" data-id = "' . $item->id . '">';
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
    
    private function getModules(){
     
        $modules = Module::where('active', 1)
                ->where('type', $this->User->type)
                ->orderBy('this_order', 'asc')
                ->get();
        $modules_actions = array();
        if ($modules) {
            foreach ($modules as $module) {
                $module->actions = explode(',', $module->actions);
                $modules_actions[] = $module;
            }
        }
        return $modules_actions;
    
    }

}
