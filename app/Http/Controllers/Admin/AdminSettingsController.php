<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;

class AdminSettingsController extends BackendController
{
    public function __construct() {
        
        parent::__construct();
        $this->middleware('CheckPermission:admin_settings,open',['only' => ['index']]);

    }

    public function index() {
        
        return $this->_view('admin_settings/index','backend');
    }
}
