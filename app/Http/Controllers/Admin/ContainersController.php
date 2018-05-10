<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Container;
use App\Models\ContainerTranslation;
use App\Models\ContainerAssignedHistory;
use App\Models\User;
use DB;
use Validator;

class ContainersController extends BackendController {

    private $rules = array(
        // 'this_order' => 'required',
        'delegate_id' => 'required',
        'active' => 'required',
        'lat' => 'required',
        'lng' => 'required',
    );

    public function __construct() {
        parent::__construct();
        $this->middleware('CheckPermission:container,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:container,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:container,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:container,delete', ['only' => ['delete']]);
    }

    public function index() {
        return $this->_view('container/index', 'backend');
    }

    public function create() {
        $this->data['delegate'] = User::where('type', 2)->where('active', 1)->get();
        return $this->_view('container/create', 'backend');
    }

    public function store(Request $request) {

        $columns_arr = array(
            'title' => 'required|unique:containers_translations,title',
        );
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        // dd($validator);
        DB::beginTransaction();
        try {

            $container = new Container;
            $container->active = $request->input('active');
            // $container->this_order = $request->input('this_order');
            $container->lat = $request->input('lat');
            $container->lng = $request->input('lng');
            $container->delegate_id = $request->input('delegate_id');
            $container->save();

            $container_translations = array();
            $title = $request->input('title');
            $address = $request->input('address');
            foreach ($title as $key => $value) {
                $location_translations[] = array(
                    'locale' => $key,
                    'title' => $value,
                    'address' => getAddress($request->input('lat'), $request->input('lng'), $lang = $key),
                    'container_id' => $container->id
                );
            }
            ContainerTranslation::insert($location_translations);

            $ContainerAssignedHistory = new ContainerAssignedHistory;
            $ContainerAssignedHistory->container_id = $container->id;
            $ContainerAssignedHistory->delegate_id = $request->input('delegate_id');
            $ContainerAssignedHistory->start = date('Y-m-d');
            $ContainerAssignedHistory->save();
            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    public function edit($id) {
        $find = Container::find($id);
        if (!$find) {
            return $this->err404();
        }
        $containerTranslation = ContainerTranslation::where('container_id', $id)->get();
        $title = $containerTranslation->pluck('title', 'locale')->all();
        $this->data['delegate'] = User::where('type', 2)->where('active', 1)->get();
        // $address = $containerTranslation->pluck('address', 'locale')->all();
        $this->data['data'] = $find;
        $this->data['title'] = $title;
        // $this->data['address'] = $address;
        return $this->_view('container/edit', 'backend');
    }

    public function update(Request $request, $id) {
        // dd($request);
        $container = Container::find($id);
        if (!$container) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $columns_arr = array(
            'title' => 'required',
        );

        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            if ($container->delegate_id != (int) $request->input('delegate_id')) {
                //dd('here2');
                ContainerAssignedHistory::whereNull('end')
                        ->where('container_id', $container->id)
                        ->where('delegate_id', $container->delegate_id)
                        ->update(['end' => date('Y-m-d')]);
                $ContainerAssignedHistory = new ContainerAssignedHistory;
                $ContainerAssignedHistory->container_id = $container->id;
                $ContainerAssignedHistory->delegate_id = $request->input('delegate_id');
                $ContainerAssignedHistory->start = date('Y-m-d');
                $ContainerAssignedHistory->save();
            }
            $container->active = $request->input('active');
            $container->lat = $request->input('lat');
            $container->lng = $request->input('lng');
            $container->delegate_id = $request->input('delegate_id');
            $container->save();
            ContainerTranslation::where('container_id', $container->id)->delete();

            $commonQuestion_translations = array();
            $title = $request->input('title');
            $address = $request->input('address');
            foreach ($title as $key => $value) {
                $container_translations[] = array(
                    'locale' => $key,
                    'title' => $value,
                    'address' => getAddress($request->input('lat'), $request->input('lng'), $lang = $key),
                    'container_id' => $container->id
                );
                ContainerTranslation::insert($container_translations);
            }


            //dd('here');
            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', $ex->getMessage(), 400);
        }
    }

    public function destroy($id) {
        $container = Container::find($id);
        if (!$container) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            ContainerTranslation::where('container_id', $container->id)->delete();
            $container->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {
        $containers = Container::join('containers_translations', 'containers.id', '=', 'containers_translations.container_id')
                ->join('users', 'users.id', '=', 'containers.delegate_id')
                ->where('containers_translations.locale', $this->lang_code)
                ->select([
            'containers.id', "containers_translations.title", "users.username as delegate", "containers.active"
        ]);

        return \Datatables::eloquent($containers)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('container', 'edit') || \Permissions::check('container', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('container', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('container.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('container', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Container.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->addColumn('active', function ($item) {
                            if ($item->active == 1) {
                                $message = _lang('app.active');
                                $class = 'label-success';
                            } else {
                                $message = _lang('app.not_active');
                                $class = 'label-danger';
                            }
                            $back = '<span class="label label-sm ' . $class . '">' . $message . '</span>';
                            return $back;
                        })
                        ->escapeColumns([])
                        ->make(true);
    }

}
