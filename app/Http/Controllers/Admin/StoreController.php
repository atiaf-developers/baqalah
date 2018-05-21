<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\User;
use App\Models\Store;
use App\Models\StoreCtegory;
use Validator;

class StoreController extends BackendController
{
    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:stores,open');
        $this->middleware('CheckPermission:stores,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:stores,view', ['only' => ['show']]);
        $this->middleware('CheckPermission:stores,edit', ['only' => ['update']]);
        $this->middleware('CheckPermission:stores,delete', ['only' => ['delete']]);
        // $this->middleware('CheckPermission:stores,open');
    }
    public function index(Request $request) {
        return $this->_view('store/index', 'backend');
    }
    public function active($id){
        
        if ($User) {
            if($User->active==1){
                $User->active=0;
            }else{
                $User->active=1;
            }
            $User->save();
            $Store = User::where('user_id',$id)->find();
            if($Store->active==1){
                $Store->active=0;
            }else{
                $Store->active=1;
            }
            $Store->save();
            return _json('success', _lang('app.success'));
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }
    public function show(Request $request,$id) {
        
        $User = User::find($id);
        if ($User) {
            return _json('success', $User);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }
    public function edit($id) {
        $data = User::join('stores','stores.user_id','users.id')
        ->where('type',2)->where('users.id',$id)->first();
        // dd($data);
        if (!$data) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        // $this->data['translations'] = NewsTranslation::where('news_id', $id)->get()->keyBy('locale');
        // $news->images = json_decode($news->images);
        $this->data['data'] = $data;
        $this->data['categories'] =StoreCtegory::
        join('categories','categories.id','store_categories.category_id')
        ->where('store_categories.store_id',$data->id)->get();
        // dd($this->data['categories']);
        return $this->_view('store/view', 'backend');
    }
    public function data(Request $request) {
        $Clients=User::join('stores','stores.user_id','users.id')
        ->where('type',2)->select(['users.id','stores.image','stores.mobile','users.active','stores.name','stores.id as store_id']);
        return \Datatables::eloquent($Clients)
        ->addColumn('options', function ($item) {

            $back = "";
            if (\Permissions::check('stores', 'view') || \Permissions::check('stores', 'delete')) {
                $back .= '<div class="btn-group">';
                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                $back .= '<i class="fa fa-angle-down"></i>';
                $back .= '</button>';
                $back .= '<ul class = "dropdown-menu" role = "menu">';
                if (\Permissions::check('stores', 'view')) {
                    $back .= '<li>';
                    $back .= '<a href="'.url('admin/stores/edit/').'/'.$item->id.'" data-id = "' . $item->id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.view');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                if (\Permissions::check('stores', 'delete')) {
                    $back .= '<li>';
                    $back .= '<a href="" data-toggle="confirmation" onclick = "Clients.delete(this);return false;" data-id = "' . $item->id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                    $back .= '</a>';
                    $back .= '</li>';
                }
                if(\Permissions::check('products', 'open')){
                    $back .= '<li>';
                    $back .= '<a href="'.url('admin/products/').'/'.$item->store_id.'" data-id = "' . $item->store_id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.products');
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
                $class = 'btn-info';
            } else {
                $message = _lang('app.not_active');
                $class = 'btn-danger';
            }
            $back = '<a class="btn ' . $class . '" onclick = "Stores.status(this);return false;" data-id = "' . $item->id . '" data-status = "' . $item->active . '">' . $message . ' <a>';
            return $back;
        }) 
        ->addColumn('image', function ($item) {
            if (!$item->image) {
                $item->image = 'default.png';
            }
             $back = '<img src="' . url('public/uploads/stores/' . $item->image) . '" style="height:64px;width:64px;"/>';
             return $back;
         })
        ->escapeColumns([])
        ->make(true);
    }
}
