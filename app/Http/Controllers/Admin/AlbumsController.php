<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Album;
use App\Models\AlbumTranslation;
use Validator;
use DB;


class AlbumsController extends BackendController {

    private $rules = array(
        'active' => 'required',
        'this_order' => 'required|unique:albums'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:albums,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:albums,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:albums,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:albums,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('albums/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        return $this->_view('albums/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {

        $columns_arr = array(
            'title' => 'required|unique:albums_translations,title',
            
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
            $album = new Album;
            $album->slug = str_slug($request->input('title')['en']);
            $album->active = $request->input('active');
            $album->this_order = $request->input('this_order');
            
            $album->save();
            
            $translations = array();
            $title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $translations[] = array(
                    'locale' => $key,
                    'title'  => $title[$key],
                    'album_id' => $album->id
                );
            }
            AlbumTranslation::insert($translations);
            DB::commit();
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
        $find = Album::find($id);

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
        $album = Album::find($id);

        if (!$album) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = AlbumTranslation::where('album_id',$id)->get()->keyBy('locale');
        $this->data['album'] = $album;

        return $this->_view('albums/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {

        $album = Album::find($id);

        if (!$album) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

       $columns_arr = array(
            'title' => 'required|unique:albums_translations,title,'.$id .',album_id',
        );
        $this->rules['this_order'] = 'required|unique:albums,this_order,'.$id;
        $lang_rules = $this->lang_rules($columns_arr);
        $this->rules = array_merge($this->rules, $lang_rules);

        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }

        DB::beginTransaction();
        try {
            $album->slug = str_slug($request->input('title')['en']);
            $album->active = $request->input('active');
            $album->this_order = $request->input('this_order');
            
            $album->save();
            
            $translations = array();

            AlbumTranslation::where('album_id', $album->id)->delete();

            $title = $request->input('title');

            foreach ($this->languages as $key => $value) {
                $translations[] = array(
                    'locale' => $key,
                    'title'  => $title[$key],
                    'album_id' => $album->id
                );
            }
            AlbumTranslation::insert($translations);

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
        $album = Album::find($id);
        if (!$album) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        //dd($album);
        DB::beginTransaction();
        try {
            $album->delete();
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

        $albums = Album::Join('albums_translations', 'albums.id', '=', 'albums_translations.album_id')
                ->where('albums_translations.locale', $this->lang_code)
                ->select([
            'albums.id', "albums_translations.title", "albums.this_order", 'albums.active',
        ]);

        return \Datatables::eloquent($albums)
        ->addColumn('options', function ($item) {

            $back = "";
            if (\Permissions::check('albums', 'edit') || \Permissions::check('albums', 'delete') || \Permissions::check('album_images', 'open')) {
                $back .= '<div class="btn-group">';
                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                $back .= '<i class="fa fa-angle-down"></i>';
                $back .= '</button>';
                $back .= '<ul class = "dropdown-menu" role = "menu">';
                if (\Permissions::check('albums', 'edit')) {
                    $back .= '<li>';
                    $back .= '<a href="' . route('albums.edit', $item->id) . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                if (\Permissions::check('albums', 'delete')) {
                    $back .= '<li>';
                    $back .= '<a href="" data-toggle="confirmation" onclick = "Albums.delete(this);return false;" data-id = "' . $item->id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                    $back .= '</a>';
                    $back .= '</li>';
                }
                if (\Permissions::check('album_images', 'open')) {
                    $back .= '<li>';
                    $back .= '<a href="'.route('album_images.index').'?album='.$item->id.'">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.album_images');
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
