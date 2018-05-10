<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\User;
use App\Models\Massage;
use App\Models\Chat;
use App\Models\Jobs;
use App\Models\ConsultationGroup;
use Validator;

class UsersController extends BackendController {

    private $rules = array(
        'fullname' => 'required',
        'username' => 'required|unique:users,username',
        'mobile' => 'required|unique:users,mobile',
        'password' => 'required',
       
    );
    private $ruels_page;
    public function __construct() {

        parent::__construct();
        $page=$_GET['type'];

        if($page=='delegates'){
          $this->ruels_page='delegates';
        }else{
          $this->ruels_page='clients';
        }
        $this->middleware('CheckPermission:'.$this->ruels_page.',open');
        $this->middleware('CheckPermission:'.$this->ruels_page.',add', ['only' => ['store']]);
        $this->middleware('CheckPermission:'.$this->ruels_page.',edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:'.$this->ruels_page.',delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {
        $page=$request->input('type');

        if($page=='clients'){
          $this->data['type']=1;
          return $this->_view('users/index', 'backend');
        }else{
          $this->data['type']=2;
          return $this->_view('worker/index', 'backend');
        }
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
        if ($request->input('email')) {
            $this->rules['email'] = 'required|email|unique:users,email';
        }
        if ($request->file('user_image')) {
             $this->rules['user_image'] = 'required|image|mimes:gif,png,jpeg|max:1000';
            
        }
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {
         
            try {
            $User = new User;
            $User->name = $request->input('fullname');
            $User->username = $request->input('username');
            $User->email = $request->input('email');
            $User->mobile = $request->input('mobile');
            $User->password = bcrypt($request->input('password'));
            $User->active = $request->input('active');
            $User->type = $request->input('type');
            if ($request->file('user_image')) {
                  $User->image = User::upload($request->file('user_image'), 'users',true);
            }
          
                $User->save();
                return _json('success', _lang('app.added_successfully'));
            } catch (\Exception $ex) {
                return _json('error', _lang('app.error_is_occured'));
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id) {
        
        
            $User = User::find($id);
            

            if ($User != null) {
                if ($request->ajax()) {
                    return _json('success', $User);
                }
                
                $this->data['user'] = $User;
                return $this->_view('worker/show', 'backend');
                
                
            } else {
                if ($request->ajax()) {
                    return _json('error', _lang('app.error_is_occured'));
                }
                return $this->err404();
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
    //   echo $id;die;
        $User = User::find($id);
        if (!$User) {
            return _json('error', _lang('app.error_is_occured'));
        }
        if ($request->file('user_image')) {
            $rules['user_image'] = 'required|image|mimes:gif,png,jpeg|max:1000';
        }
        if ($request->input('email')) {
            $rules['email'] = "required|unique:users,email,$User->id";
        }
        $rules['username'] = "required|unique:users,username,$User->id";
       
        $rules['mobile'] = "required|unique:users,mobile,$User->id";
        if ($request->input('password') === null) {
            unset($rules['password']);
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {
          $User->name = $request->input('fullname');
          $User->username = $request->input('username');
          $User->email = $request->input('email');
          $User->mobile = $request->input('mobile');
            if ($request->input('password')) {
                $User->password = bcrypt($request->input('password'));
            }
            $User->active = $request->input('active');
            if ($request->file('user_image')) {
                $old_image = $User->user_image;
                if ($old_image != 'default.png') {
                    User::deleteUploaded('users',$old_image);
                }
                $User->image = User::upload($request->file('user_image'), 'users',true);
            }
            try {
                $User->save();
                return _json('success', _lang('app.updated_successfully'));
            } catch (Exception $ex) {
                return _json('error', _lang('app.error_is_occured'));
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function destroy($id) {
        $User = User::find($id);
        if ($User == null) {
            return _json('error', _lang('app.error_is_occured'));
        }
        try {

            $User->delete();
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
        $type = $request->input('type');
        $user = User::where('type', $type)->select('id', 'email', 'type', 'username','name', 'mobile', 'active','image')
        ->where('type',$type);

        return \Datatables::eloquent($user)
                ->addColumn('options', function ($item){
                    if($item->type==1){
                        $js='Users';
                    }else{
                        $js='Worker';
                    }
                    $back = "";

                        if (\Permissions::check($this->ruels_page, 'edit') || \Permissions::check($this->ruels_page, 'delete')) {
                            $back .= '<div class="btn-group">';
                            $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                            $back .= '<i class="fa fa-angle-down"></i>';
                            $back .= '</button>';
                            $back .= '<ul class = "dropdown-menu" role = "menu">';
                            if (\Permissions::check($this->ruels_page, 'edit')) {
                                $back .= '<li>';
                                $back .= '<a href="" onclick = "'.$js.'.edit(this);return false;" data-id = "' . $item->id . '">';
                                $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                $back .= '</a>';
                                $back .= '</li>';
                            }
                            if (\Permissions::check($this->ruels_page, 'open') && $item->type == 2) {
                                $back .= '<li>';
                                $back .= '<a href="'.route('users.show',$item->id).'?type=delegates" onclick = "" data-id = "' . $item->id . '">';
                                $back .= '<i class = "icon-docs"></i>' . _lang('app.show');
                                $back .= '</a>';
                                $back .= '</li>';
                            }
                            if (\Permissions::check($this->ruels_page, 'delete')) {
                                $back .= '<li>';
                                $back .= '<a href="" data-toggle="confirmation" onclick = "'.$js.'.delete(this);return false;" data-id = "' . $item->id . '">';
                                $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                $back .= '</a>';
                                $back .= '</li>';
                            }
                            $back .= '</ul>';
                            $back .= ' </div>';
                        }


                    return $back;
                })
                ->addColumn('active', function ($item) {
                    if($item->type==1){
                        $js='Users';
                    }else{
                        $js='Worker';
                    }
                    if ($item->active == 1) {
                        $message = _lang('app.active');
                        $class = 'btn-info';
                    } else {
                        $message = _lang('app.not_active');
                        $class = 'btn-danger';
                    }
                    $back = '<a class="btn ' . $class . '" onclick = "'.$js.'.status(this);return false;" data-id = "' . $item->id . '" data-status = "' . $item->active . '">' . $message . ' <a>';
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
    // public function get_country(){
    //   $Countrys = Country::select([
    //     'id', "title_en","title_ar", "this_order", 'active', 'image','country_id'
    //   ])->whereNull('country_id')->get();
    // }
    public function get_city($id){

    }
    public function get_state($id){

    }
    // public function get_jobs(){
    //   $jobs = Jobs::select([
    //               'id', "title_ar", "title_en", "this_order", 'active','main_job'
    //   ])->whereNull('main_job')->get();
    //   return $jobs;
    // }
    // public function First_subJob(){
    //   $jobs = Jobs::select([
    //               'id', "title_ar", "title_en", "this_order", 'active','main_job'
    //   ])->whereNotNull('main_job')->get();
    //   return $jobs;
    // }
    // public function get_subJob($id){
    //   $jobs = Jobs::select([
    //               'id', "title_ar", "title_en", "this_order", 'active'
    //   ])->where('main_job',$id);
    // }


}
