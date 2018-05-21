<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Product;
use App\Models\Store;
use App\Models\Category;
use Validator;

class ProductController extends BackendController
{
    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:products,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:products,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:products,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:products,delete', ['only' => ['delete']]);
    }

    public function index(Request $request){

        $store_id = $request->input('store_id') ? $request->input('store_id') : null;
        $category_id = $request->input('category_id') ? $request->input('category_id') : null;

        $this->data['store_name'] = null;
        $this->data['store_id'] = null;
        $this->data['category_id'] = null;
        $this->data['category_name'] = null;

        if ($store_id) {
            $store = Store::find($store_id);
            if (!$store) {
                return $this->err404();
            }
            $this->data['store_name'] = $store->name;
            $this->data['store_id'] = $store_id;
        }else if($category_id){
            $category = Category::join('categories_translations','categories.id','=','categories_translations.category_id')
            ->where('categories.id',$category_id)
            ->where('categories_translations.locale',$this->lang_code)
            ->select('categories.id','categories_translations.title')
            ->first();
            if (!$category) {
                return $this->err404();
            }
            $this->data['category_id'] = $category_id;
            $this->data['category_name'] = $category->title;
        }

        return $this->_view('products/index', 'backend');
    }

    public function status($id){
        try {
            $product = Product::find($id);
            if (!$product) {
                return _json('error', _lang('app.not_found'));
            }  
            $product->active = !$product->active;
            $product->save();
            return _json('success', _lang('app.success'));
        } catch (\Exception $e) {
            return _json('error', _lang('app.error_is_occured'));
        }
    }

    public function show($id) {

        $product =Product::join('categories','categories.id','products.category_id')
            ->join('categories_translations','categories.id','=','categories_translations.category_id')
            ->where('categories_translations.locale',$this->lang_code)
            ->where('products.id',$id)
            ->select('products.*','categories_translations.title as category')
            ->first();
        if (!$product) {
            return $this->err404();
        }
        $this->data['product'] = $product;
         
        return $this->_view('products/view', 'backend');
    }

    public function data(Request $request){

        $store_id = $request->input('store_id') ? $request->input('store_id') : null;
        $category_id = $request->input('category_id') ? $request->input('category_id') : null;

        $Products=  Product::Join('categories','products.category_id','=','categories.id')
        ->join('categories_translations','categories.id','=','categories_translations.category_id')
        ->join('stores','products.store_id','=','stores.id')
        ->where('categories_translations.locale',$this->lang_code);
        if ($store_id) {
            $Products->where('products.store_id',$request->input('store_id'));
        }else if($category_id){
            $Products->where('products.category_id',$request->input('category_id'));
        }
        $Products = $Products->select('products.id','products.name','products.price','products.active','products.images','stores.name as store','categories_translations.title as category');


        return \Datatables::eloquent($Products)
        ->addColumn('options', function ($item) {
            $back = "";
            if (\Permissions::check('products', 'edit') || \Permissions::check('products', 'delete')) {
                $back .= '<div class="btn-group">';
                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                $back .= '<i class="fa fa-angle-down"></i>';
                $back .= '</button>';
                $back .= '<ul class = "dropdown-menu" role = "menu">';
                if (\Permissions::check('products', 'edit')) {
                    $back .= '<li>';
                    $back .= '<a href="'.url('admin/products/').'/'.$item->id.'" data-id = "' . $item->id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.show');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                if (\Permissions::check('products', 'delete')) {
                    $back .= '<li>';
                    $back .= '<a href="" data-toggle="confirmation" onclick = "Products.delete(this);return false;" data-id = "' . $item->id . '">';
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
            $prefixed_array = preg_filter('/^/', url('public/uploads/products') . '/', json_decode($item->images));
            $back = '<img src="' .  $prefixed_array[0] . '" style="height:64px;width:64px;"/>';
            return $back;
        })
        ->addColumn('category', function ($item) { 
            $back = $item->category;
            return $back;
        })
        ->addColumn('store', function ($item) { 
            $back = $item->store;
            return $back;
        })
        ->escapeColumns([])
        ->make(true);
    }
}
