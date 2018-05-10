<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\AlbumImage;
use App\Models\Album;
use Validator;

class AlbumImagesController extends BackendController {

    private $rules = array(
        'album_id' => 'required',
        'image' => 'required|image|mimes:gif,png,jpeg|max:1000',
        
    );
    public function __construct() {

        parent::__construct();

        $this->middleware('CheckPermission:album_images,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:album_images,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:album_images,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:album_images,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {

        $this->data['album'] = Album::join('albums_translations','albums_translations.album_id','=','albums.id')
                       ->where('albums.id',$request->input('album'))
                       ->where('albums_translations.locale',$this->lang_code)
                       ->select('albums.id','albums_translations.title')
                       ->first();

        return $this->_view('album_images/index', 'backend');
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
         $this->rules['this_order'] = "required|unique:album_images,this_order,Null,id,album_id,{$request->album_id}";
        $validator = Validator::make($request->all(), $this->rules);
       
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } 
           try {
            $album_image = new AlbumImage;
            $album_image->album_id = $request->input('album_id');
            $album_image->image = AlbumImage::upload($request->file('image'), 'albums',true);
            $album_image->this_order = $request->input('this_order');
            $album_image->save();
            return _json('success', _lang('app.added_successfully'));
            } catch (Exception $ex) {
                return _json('error', _lang('app.error_is_occured'));
            }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {

        $album_image = AlbumImage::find($id);

        if ($album_image != null) {
            return _json('success', $album_image);
        } else {
            return _json('error', _lang('app.error_is_occured'));
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
    
        $album_image = AlbumImage::find($id);
        if (!$album_image) {
            return _json('error', _lang('app.error_is_occured'));
        }
        if ($request->file('image')) {
            $this->rules['image'] = 'required|image|mimes:gif,png,jpeg|max:1000';
        }
        else{
            unset( $this->rules['image']);
        }
      
        $this->rules['this_order'] = "required|unique:album_images,this_order,{$id},id,album_id,{$request->album_id}";
        $validator = Validator::make($request->all(), $this->rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }  
        try {
            if ($request->file('image')) {
                AlbumImage::deleteUploaded('album_images', $album_image->image);
                $album_image->image = AlbumImage::upload($request->file('image'), 'albums',true);
            }
            $album_image->this_order = $request->input('this_order');
            $album_image->save();
            return _json('success', _lang('app.added_successfully'));
            } catch (Exception $ex) {
                return _json('error', _lang('app.error_is_occured'));
            }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id) {
        $album_image = AlbumImage::find($id);
        if (!$album_image) {
            return _json('error', _lang('app.error_is_occured'));
        }
        try {
              $album_image->delete();
              return _json('success', _lang('app.deleted_successfully'));
        }  
         catch (\Exception $ex) {
            return _json('error', _lang('app.error_is_occured'));
        }
    }
    public function data(Request $request) {
        $album_id = $request->input('album_id');
        $album_images = AlbumImage::where('album_id', $album_id)->select('id', 'image', 'this_order');
       
        return \Datatables::eloquent($album_images)
                ->addColumn('options', function ($item){
                    $back = "";

                        if (\Permissions::check('album_images', 'edit') || \Permissions::check('album_images', 'delete')) {
                            $back .= '<div class="btn-group">';
                            $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                            $back .= '<i class="fa fa-angle-down"></i>';
                            $back .= '</button>';
                            $back .= '<ul class = "dropdown-menu" role = "menu">';
                            if (\Permissions::check('album_images', 'edit')) {
                                $back .= '<li>';
                                $back .= '<a href="" onclick = "AlbumImages.edit(this);return false;" data-id = "' . $item->id . '">';
                                $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                $back .= '</a>';
                                $back .= '</li>';
                            }
                            if (\Permissions::check('album_images', 'delete')) {
                                $back .= '<li>';
                                $back .= '<a href="" data-toggle="confirmation" onclick = "AlbumImages.delete(this);return false;" data-id = "' . $item->id . '">';
                                $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                $back .= '</a>';
                                $back .= '</li>';
                            }
                            $back .= '</ul>';
                            $back .= ' </div>';
                        }


                    return $back;
                })
                ->editColumn('image', function ($item) {
                    $back = '<img src="' . url('public/uploads/albums/' . $item->image) . '" style="height:64px;width:64px;"/>';
                    return $back;
                })
                ->escapeColumns([])
                ->make(true);
    }
 


}
