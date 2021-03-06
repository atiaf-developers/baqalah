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
        $this->middleware('CheckPermission:clients,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:clients,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:clients,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:clients,delete', ['only' => ['delete']]);
    }
    public function index(Request $request) {
        return $this->_view('clients/index', 'backend');
    }
    public function status($id){
        try {
            $user = User::find($id);
            $user->active = !$user->active;
            $user->save();
            return _json('success', _lang('app.success'));
        } catch (\Exception $e) {
            return _json('error', _lang('app.error_is_occured'));
        }
    }

    public function show($id) {
        
        $user = User::find($id);
        if (!$user) {
            return $this->err404();
        } 
        $this->data['user'] = $user;
        return $this->_view('clients/view', 'backend');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $user = User::find($id);
        if (!$user) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        try {
            $user->delete();
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
        $Clients = User::where('type',1)->orderBy('id','desc');

        return \Datatables::eloquent($Clients)
        ->addColumn('options', function ($item) {

            $back = "";
            if (\Permissions::check('clients', 'edit') || \Permissions::check('clients', 'delete')) {
                $back .= '<div class="btn-group">';
                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                $back .= '<i class="fa fa-angle-down"></i>';
                $back .= '</button>';
                $back .= '<ul class = "dropdown-menu" role = "menu">';
                if (\Permissions::check('clients', 'edit')) {
                    $back .= '<li>';
                    $back .= '<a href="'.url('admin/clients/').'/'.$item->id.'" data-id = "' . $item->id . '">';
                    $back .= '<i class = "icon-docs"></i>' . _lang('app.show');
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
                if ($item->gender == 1) {
                    $item->image = 'default_male.png';
                }else if($item->gender == 2){
                    $item->image = 'default_female.png';
                } 
            }
             $back = '<img src="' . url('public/uploads/users/' . $item->image) . '" style="height:64px;width:64px;"/>';
             return $back;
         })
        ->escapeColumns([])
        ->make(true);
    }
}
