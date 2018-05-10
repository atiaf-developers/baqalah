<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Video;
use App\Models\VideoTranslation;
use Validator;
use DB;


class VideosController extends BackendController {

    private $rules = array(
        'url' => 'required|url',
        'active' => 'required',
        'this_order' => 'required|unique:videos'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:videos,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:videos,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:videos,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:videos,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('videos/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('videos/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $columns_arr = array(
            'title' => 'required|unique:videos_translations,title',
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
            $video = new Video;
            $video->slug = str_slug($request->input('title')['en']);
            $video->active = $request->input('active');
            $video->this_order = $request->input('this_order');
            $video->url = $request->input('url');
            $video->youtube_url = $request->input('youtube_url');
            
            $video->save();
            
            $video_translations = array();
            $video_title = $request->input('title');
            foreach ($this->languages as $key => $value) {
                $video_translations[] = array(
                    'locale' => $key,
                    'title'  => $video_title[$key],
                    'video_id' => $video->id
                );
            }
            VideoTranslation::insert($video_translations);
            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
             DB::rollback();
             dd($ex);
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
        $find = Video::find($id);

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
        $video = Video::find($id);

        if (!$video) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->data['video'] = $video;
       $this->data['translations'] = VideoTranslation::where('video_id',$id)->get()->keyBy('locale');
        
       
       

        return $this->_view('videos/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $video = Video::find($id);

        if (!$video) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
       $columns_arr = array(
            'title' => 'required|unique:videos_translations,title,'.$id .',video_id',
        );
        $this->rules['this_order'] = 'required|unique:videos,this_order,'.$id;
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {
            $video->slug = str_slug($request->input('title')['en']);
            $video->active = $request->input('active');
            $video->this_order = $request->input('this_order');
            $video->url = $request->input('url');
            $video->youtube_url = $request->input('youtube_url');
            $video->save();
            
            $video_translations = array();

            VideoTranslation::where('video_id', $video->id)->delete();

            $video_title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $video_translations[] = array(
                    'locale' => $key,
                    'title'  => $video_title[$key],
                    'video_id' => $video->id
                );
            }
            VideoTranslation::insert($video_translations);

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
        $video = Video::find($id);
        if (!$video) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $video->delete();
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

        $videos = Video::Join('videos_translations', 'videos.id', '=', 'videos_translations.video_id')
                ->where('videos_translations.locale', $this->lang_code)
                ->select([
            'videos.id', "videos_translations.title", "videos.this_order", 'videos.active','videos.url'
        ]);

        return \Datatables::eloquent($videos)
        ->addColumn('options', function ($item) {

            $back = "";
            if (\Permissions::check('videos', 'edit') || \Permissions::check('videos', 'delete')) {
                $back .= '<div class="btn-group">';
                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                $back .= '<i class="fa fa-angle-down"></i>';
                $back .= '</button>';
                $back .= '<ul class = "dropdown-menu" role = "menu">';
                if (\Permissions::check('videos', 'edit')) {
                    $back .= '<li>';
                    $back .= '<a href="' . route('videos.edit', $item->id) . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                if (\Permissions::check('videos', 'delete')) {
                    $back .= '<li>';
                    $back .= '<a href="" data-toggle="confirmation" onclick = "Videos.delete(this);return false;" data-id = "' . $item->id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                $back .= '</ul>';
                $back .= ' </div>';
            }
            return $back;
        })
         ->editColumn('title', function ($item) {
                          
                          $back = '<a href="'.$item->url.'" target="_blank">' . $item->title . '</a>';
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
