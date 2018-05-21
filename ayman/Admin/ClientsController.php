<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\User;
use Validator;

class ClientsController extends BackendController
{
    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:clients,open');
        $this->middleware('CheckPermission:clients,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:clients,view', ['only' => ['show']]);
        $this->middleware('CheckPermission:clients,edit', ['only' => ['update']]);
        $this->middleware('CheckPermission:clients,delete', ['only' => ['delete']]);
    }
    public function index(Request $request) {
        return $this->_view('clients/index', 'backend');
    }
    public function active($id){
        $User = User::find($id);
        if ($User) {
            if($User->active==1){
                $User->active=0;
            }else{
                $User->active=1;
            }
            $User->save();
            return _json('success', _lang('app.success'));
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }
    public function edit($id) {
        $data =User::where('type',1)->where('users.id',$id)->first();
        // dd($data);
        if (!$data) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        // $this->data['translations'] = NewsTranslation::where('news_id', $id)->get()->keyBy('locale');
        // $news->images = json_decode($news->images);
        $this->data['data'] = $data;
        // dd($data);
        return $this->_view('clients/view', 'backend');
    }
    public function show(Request $request,$id) {
        
        $User = User::find($id);
        if ($User) {
            return _json('success', $User);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }
    public function data(Request $request) {
        $Clients=User::where('type',1);
        return \Datatables::eloquent($Clients)
        ->addColumn('options', function ($item) {

            $back = "";
            if (\Permissions::check('clients', 'view') || \Permissions::check('clients', 'delete')) {
                $back .= '<div class="btn-group">';
                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                $back .= '<i class="fa fa-angle-down"></i>';
                $back .= '</button>';
                $back .= '<ul class = "dropdown-menu" role = "menu">';
                if (\Permissions::check('clients', 'view')) {
                    $back .= '<li>';
                    $back .= '<a href="'.url('admin/clients/edit/').'/'.$item->id.'" data-id = "' . $item->id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.view');
                    $back .= '</a>';
                    $back .= '</li>';
                }

                if (\Permissions::check('clients', 'delete')) {
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
            $back = '<a class="btn ' . $class . '" onclick = "Clients.status(this);return false;" data-id = "' . $item->id . '" data-status = "' . $item->active . '">' . $message . ' <a>';
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
