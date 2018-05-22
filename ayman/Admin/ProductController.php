<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Product;
use App\Models\Store;
use Validator;

class ProductController extends BackendController
{
    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:products,open');
        $this->middleware('CheckPermission:products,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:products,view', ['only' => ['show']]);
        $this->middleware('CheckPermission:products,edit', ['only' => ['update']]);
        $this->middleware('CheckPermission:products,delete', ['only' => ['delete']]);
    }

    public function index($id){
        $this->data['store_id']=$id;
        return $this->_view('products/index', 'backend');
    }
    public function active($id){
        $Product = Product::find($id);
        // dd($Product);
        if ($Product) {
            if($Product->active==1){
                $Product->active=0;
            }else{
                $Product->active=1;
            }
            $Product->save();
            return _json('success', _lang('app.success'));
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }
    public function edit($id) {
        $data =Product::join('categories','categories.id','products.category_id')
        ->where('products.id',$id)
        ->select(['categories.slug as cat_name','products.*'])
        ->first();
        // dd(json_decode($data->images));
        if (!$data) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->data['data'] = $data;
        // dd($data);
        return $this->_view('products/view', 'backend');
    }
    public function data(Request $request){
        $Products=Product::where('store_id',$request->store_id);
        return \Datatables::eloquent($Products)
        ->addColumn('options', function ($item) {
            $back = "";
            if (\Permissions::check('products', 'view') || \Permissions::check('products', 'delete')) {
                $back .= '<div class="btn-group">';
                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                $back .= '<i class="fa fa-angle-down"></i>';
                $back .= '</button>';
                $back .= '<ul class = "dropdown-menu" role = "menu">';
                if (\Permissions::check('products', 'view')) {
                    $back .= '<li>';
                    $back .= '<a href="'.url('admin/products/edit/').'/'.$item->id.'" data-id = "' . $item->id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.view');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                if (\Permissions::check('products', 'delete')) {
                    $back .= '<li>';
                    $back .= '<a href="" data-toggle="confirmation" onclick = "Clients.delete(this);return false;" data-id = "' . $item->id . '">';
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
                $class = 'btn-info';
            } else {
                $message = _lang('app.not_active');
                $class = 'btn-danger';
            }
            $back = '<a class="btn ' . $class . '" onclick = "Proudcts.status(this);return false;" data-id = "' . $item->id . '" data-status = "' . $item->active . '">' . $message . ' <a>';
            return $back;
        }) 
        ->addColumn('image', function ($item) {
            if (!$item->image) {
                $item->image = 'default.png';
            }
             $back = '<img src="' . url('public/uploads/users/' . $item->image) . '" style="height:64px;width:64px;"/>';
             return $back;
         })
        ->escapeColumns([])
        ->make(true);
    }
}