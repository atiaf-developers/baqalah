<?php

use Illuminate\Http\Request;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */

Route::get('user', function (Request $request) {
    return _api_json(false, 'user');
})->middleware('jwt.auth');
Route::group(['namespace' => 'Api'], function () {



    Route::get('token', 'BasicController@getToken');
    

    Route::post('login', 'LoginController@login');
    Route::post('register', 'RegisterController@register');

    Route::get('setting', 'BasicController@getSettings');
    Route::get('get_categories', 'BasicController@getCategories');

    Route::group(['middleware' => 'jwt.auth'], function () {

        Route::post('user/update', 'UserController@update');
        Route::get('logout', 'UserController@logout');
        
        Route::get('store_categories', 'BasicController@getStoreCategories');
        Route::get('get_user', 'UserController@getUser');
        Route::post('rate', 'UserController@rate');
        Route::get('favourites', 'UserController@favourites');
        Route::post('handle_favourites','UserController@handleFavourites');
        Route::post('send_complaint', 'BasicController@sendContactMessage');
        Route::get('complaints', 'BasicController@getComplaints');

        Route::post('change_status','OrdersController@status');
        
       

        Route::resource('products', 'ProductsController');
        Route::resource('stores', 'StoresController');
        Route::resource('cart','CartController');
        Route::resource('orders','OrdersController');
        

        
    });
});
