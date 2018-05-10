<?php

namespace App\Http\Controllers\Front\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;

class DashboardController extends FrontController {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $view = 'customer/index';
        return $this->_view($view);
    }

}
