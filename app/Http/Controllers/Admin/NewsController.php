<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\News;
use App\Models\NewsTranslation;
use App\Events\Noti;
use Validator;
use DB;
use App\Helpers\Fcm;

class NewsController extends BackendController {

    private $rules = array(
        'images.0' => 'required|image|mimes:gif,png,jpeg|max:1000',
        'images.1' => 'image|mimes:gif,png,jpeg|max:1000',
        'images.2' => 'image|mimes:gif,png,jpeg|max:1000',
        'images.3' => 'image|mimes:gif,png,jpeg|max:1000',
        'images.4' => 'image|mimes:gif,png,jpeg|max:1000',
        'active' => 'required',
        'this_order' => 'required|unique:news'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:news,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:news,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:news,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:news,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('news/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('news/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $columns_arr = array(
            'title' => 'required|unique:news_translations,title',
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
                $images[] = News::upload($one, 'news', true);
            }
            $news = new News;
            $news->slug = str_slug($request->input('title')['en']);
            $news->active = $request->input('active');
            $news->this_order = $request->input('this_order');
            $news->images = json_encode($images);

            $news->save();

            $news_translations = array();
            $news_title = $request->input('title');
            $news_description = $request->input('description');

            foreach ($this->languages as $key => $value) {
                $news_translations[] = array(
                    'locale' => $key,
                    'title' => $news_title[$key],
                    'description' => $news_description[$key],
                    'news_id' => $news->id
                );
            }
            NewsTranslation::insert($news_translations);

            $this->create_noti($news->id, null, 6, 1);
            DB::commit();

            $message['message_ar'] = 'خبر جديد ' . $news_title['ar'];
            $message['message_en'] = 'new news ' . $news_title['en'];
            $notification = array('title' => _lang('app.keswa'), 'body' => $message, 'type' => 3, 'id' => $news->id);

            $Fcm = new Fcm;
            $token = '/topics/keswa_and';
            $Fcm->send($token, $notification, 'and');

            $token = '/topics/keswa_ios';
            $Fcm->send($token, $notification, 'ios');

            $message = _lang('app.new_news') . ' ' . $news_title[$this->lang_code];
            $url = _url('news-and-events/' . $news->slug);
            event(new Noti(['user_id' => null, 'type' => 6, 'body' => $message, 'url' => $url]));

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
        $find = News::find($id);

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
        $news = News::find($id);

        if (!$news) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = NewsTranslation::where('news_id', $id)->get()->keyBy('locale');
        $news->images = json_decode($news->images);
        $this->data['news'] = $news;

        return $this->_view('news/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $news = News::find($id);

        if (!$news) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }


        unset($this->rules['images.0']);

        $this->rules['this_order'] = 'required|unique:news,this_order,' . $id;
        $columns_arr = array(
            'title' => 'required|unique:news_translations,title,' . $id . ',news_id',
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
            $images = json_decode($news->images);
            if ($request->file('images')) {
                foreach ($request->file('images') as $key => $one) {
                    if (isset($images[$key])) {
                        News::deleteUploaded('news', $images[$key]);
                    }

                    $images[$key] = News::upload($one, 'news', true);
                }
            }
            $news->slug = str_slug($request->input('title')['en']);
            $news->active = $request->input('active');
            $news->this_order = $request->input('this_order');
            $news->images = json_encode($images);
            $news->save();

            $news_translations = array();

            NewsTranslation::where('news_id', $news->id)->delete();

            $news_title = $request->input('title');
            $news_description = $request->input('description');

            foreach ($this->languages as $key => $value) {
                $news_translations[] = array(
                    'locale' => $key,
                    'title' => $news_title[$key],
                    'description' => $news_description[$key],
                    'news_id' => $news->id
                );
            }
            NewsTranslation::insert($news_translations);

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
        $news = News::find($id);
        if (!$news) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            $news->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                dd($ex);
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {

        $news = News::Join('news_translations', 'news.id', '=', 'news_translations.news_id')
                ->where('news_translations.locale', $this->lang_code)
                ->select([
            'news.id', "news_translations.title", "news.this_order", 'news.active',
        ]);

        return \Datatables::eloquent($news)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('news', 'edit') || \Permissions::check('news', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('news', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('news.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('news', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "News.delete(this);return false;" data-id = "' . $item->id . '">';
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
