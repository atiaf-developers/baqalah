<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Order;
use DB;

class OrdersController extends ApiController {

    public function __construct() {
        parent::__construct();
    }

    public function store(Request $request)
    {
        
    }

}
