<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BackendController;
use Illuminate\Http\Request;
//use App\Http\Middleware\CheckPermission;
//use App\Lib\Permissions;
use DB;
use Auth;

class AdminController extends BackendController {

    public function __construct() {
        
        parent::__construct();

    }

   public function index() {

        return $this->_view('index','backend');
    }
    public function error() {
        return view('main_content/backend/err404');
    }
    
   
  
}
