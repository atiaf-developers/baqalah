<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Activity;
use App\Models\ActivityTranslation;
use App\Events\Noti;
use Validator;
use DB;
use App\Helpers\Fcm;

class ActivitiesController extends BackendController {

    private $rules = array(
        'images.0' => 'required|image|mimes:gif,png,jpeg|max:1000',
        'images.1' => 'image|mimes:gif,png,jpeg|max:1000',
        'images.2' => 'image|mimes:gif,png,jpeg|max:1000',
        'images.3' => 'image|mimes:gif,png,jpeg|max:1000',
        'images.4' => 'image|mimes:gif,png,jpeg|max:1000',
        'active' => 'required',
        'this_order' => 'required|unique:activities'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:activities,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:activities,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:activities,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:activities,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('activities/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('activities/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $columns_arr = array(
            'title' => 'required|unique:activities_translations,title',
            'description' => 'required'
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
            $images = [];
            foreach ($request->file('images') as $one) {
                $images[] = Activity::upload($one, 'activities', true);
            }
            $activity = new Activity;
            $activity->slug = str_slug($request->input('title')['en']);
            $activity->active = $request->input('active');
            $activity->this_order = $request->input('this_order');
            $activity->images = json_encode($images);

            $activity->save();

            $activity_translations = array();
            $activity_title = $request->input('title');
            $activity_description = $request->input('description');

            foreach ($this->languages as $key => $value) {
                $activity_translations[] = array(
                    'locale' => $key,
                    'title' => $activity_title[$key],
                    'description' => $activity_description[$key],
                    'activity_id' => $activity->id
                );
            }
            ActivityTranslation::insert($activity_translations);
            $this->create_noti($activity->id, null, 5, 1);

            DB::commit();

            $message['message_ar'] = 'نشاط جديد ' . $activity_title['ar'];
            $message['message_en'] = 'new ativity ' . $activity_title['en'];
            $notification = array('title' => _lang('app.keswa'), 'body' => $message, 'type' => 2, 'id' => $activity->id);

            $Fcm = new Fcm;
            $token = '/topics/keswa_and';
            $Fcm->send($token, $notification, 'and');

            $token = '/topics/keswa_ios';
            $Fcm->send($token, $notification, 'ios');
            $message = _lang('app.new_activity') . ' ' . $activity_title[$this->lang_code];
            $url = _url('corporation-activities/' . $activity->slug);
            event(new Noti(['user_id' => null, 'type' => 6, 'body' => $message, 'url' => $url]));


            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $find = Activity::find($id);

        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $activity = Activity::find($id);

        if (!$activity) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $activity->images = json_decode($activity->images);
        $this->data['translations'] = ActivityTranslation::where('activity_id', $id)->get()->keyBy('locale');

        $this->data['activity'] = $activity;

        return $this->_view('activities/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $activity = Activity::find($id);

        if (!$activity) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['images.0'] = 'image|mimes:gif,png,jpeg|max:1000';
        $this->rules['this_order'] = 'required|unique:activities,this_order,' . $id;
        $columns_arr = array(
            'title' => 'required|unique:activities_translations,title,' . $id . ',activity_id',
            'description' => 'required'
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
            $images = json_decode($activity->images);
            if ($request->file('images')) {
                foreach ($request->file('images') as $key => $one) {
                    if (isset($images[$key])) {
                        Activity::deleteUploaded('activities', $images[$key]);
                    }

                    $images[$key] = Activity::upload($one, 'activities', true);
                }
            }
            $activity->slug = str_slug($request->input('title')['en']);
            $activity->active = $request->input('active');
            $activity->this_order = $request->input('this_order');
            $activity->images = json_encode($images);
            $activity->save();

            $activity_translations = array();

            ActivityTranslation::where('activity_id', $activity->id)->delete();

            $activity_title = $request->input('title');
            $activity_description = $request->input('description');

            foreach ($this->languages as $key => $value) {
                $activity_translations[] = array(
                    'locale' => $key,
                    'title' => $activity_title[$key],
                    'description' => $activity_description[$key],
                    'activity_id' => $activity->id
                );
            }
            ActivityTranslation::insert($activity_translations);

            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $activity = Activity::find($id);
        if (!$activity) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $activity->delete();
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

        $activities = Activity::Join('activities_translations', 'activities.id', '=', 'activities_translations.activity_id')
                ->where('activities_translations.locale', $this->lang_code)
                ->select([
            'activities.id', "activities_translations.title", "activities.this_order", 'activities.active',
        ]);

        return \Datatables::eloquent($activities)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('activities', 'edit') || \Permissions::check('activities', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('activities', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('activities.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('activities', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Activities.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->editColumn('active', function ($item) {
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
