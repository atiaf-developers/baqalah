<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\User;
use App\Models\Store;
use App\Models\StoreCtegory;
use Validator;
use DB;

class StoresController extends BackendController
{
    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:stores,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:stores,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:stores,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:stores,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        return $this->_view('store/index', 'backend');
    }

    public function show($id) {
        
       try {
           $store = Store::Join('users','stores.user_id','=','users.id')
                          ->where('stores.id',$id)
                          ->select('stores.*','users.username','users.email')
                          ->first();

            if (!$store) {
                return $this->err404();
            }
            $store_categories = $store->categories()
                            ->join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
                            ->where('categories_translations.locale', $this->lang_code)
                            ->where('categories.active', true)
                            ->pluck('categories_translations.title');

            $this->data['store'] = $store;
            $this->data['store_categories'] = $store_categories;

           return $this->_view('store/view', 'backend');
       } catch (\Exception $e) {
           return redirect()->back();
       }
    }

    public function status($id){
        DB::beginTransaction();
        try {
            $store = Store::find($id);
            if (!$store) {
                return _json('error', _lang('app.not_found'));
            }  
            $store->active = !$store->active;
            $store->save();

            $store_user = User::find($store->user_id);
            $store_user->active = !$store_user->active;
            $store_user->save();

            DB::commit();
            return _json('success', _lang('app.success'));
        } catch (\Exception $e) {
            DB::rollback();
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
        $store = Store::find($id);
        if (!$store) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        try {
            $store->delete();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {
        $stores = Store::select('*')->orderBy('id','desc');

        return \Datatables::eloquent($stores)

        ->addColumn('options', function ($item) {
            $back = "";
            if (\Permissions::check('stores', 'edit') || \Permissions::check('stores', 'delete')) {
                $back .= '<div class="btn-group">';
                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                $back .= '<i class="fa fa-angle-down"></i>';
                $back .= '</button>';
                $back .= '<ul class = "dropdown-menu" role = "menu">';
                if (\Permissions::check('stores', 'edit')) {
                    $back .= '<li>';
                    $back .= '<a href="'.url('admin/stores/').'/'.$item->id.'" data-id = "' . $item->id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.show');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                if (\Permissions::check('stores', 'delete')) {
                    $back .= '<li>';
                    $back .= '<a href="" data-toggle="confirmation" onclick = "Stores.delete(this);return false;" data-id = "' . $item->id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                    $back .= '</a>';
                    $back .= '</li>';
                }
                if(\Permissions::check('products', 'open')){
                    $back .= '<li>';
                    $back .= '<a href="'.url('admin/products').'?store_id='.$item->id.'" data-id = "' . $item->id . '">';
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
        ->editColumn('image', function ($item) {
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
