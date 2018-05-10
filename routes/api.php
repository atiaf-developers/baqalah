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



    Route::get('/token', 'BasicController@getToken');
    Route::get('/settings', 'BasicController@getSettings');

    
    Route::get('get_albums', 'BasicController@getAlbums');
    Route::get('get_category', 'BasicController@getCategory');
    Route::get('get_news', 'BasicController@getNews');
    Route::get('get_news_detailes/{id}', 'BasicController@getNewsDetailes');
    Route::get('get_donation_types', 'BasicController@getDonationTypes');
    Route::get('get_activities', 'BasicController@getActivities');
    Route::get('get_activity_detailes/{id}', 'BasicController@getActivityDetailes');
    Route::get('get_videos', 'BasicController@getVideos');
    Route::get('get_communication_guides', 'BasicController@getCommunicationGuides');
    Route::post('send_contact_message', 'BasicController@sendContactMessage');


    Route::post('donation_requests', 'DonationRequestsController@store');


    Route::post('login', 'LoginController@login');
    Route::post('register', 'RegisterController@register');
    Route::post('send_verification_code', 'RegisterController@sendVerificationCode');

    Route::get('setting', 'BasicController@getSettings');
    Route::get('notifications', 'NotificationsController@index');
    Route::get('noti_count', 'NotificationsController@getUnReadNoti');
    Route::group(['middleware' => 'jwt.auth'], function () {

        Route::post('user/update', 'UserController@update');
        Route::get('get_categories', 'BasicController@getCategories');

        Route::resource('products', 'ProductsController');


        Route::get('store_categories', 'StoresController@getStoreCategories');
        Route::get('get_store','StoresController@getStore');
        
        Route::get('get_user', 'UserController@getUser');
        Route::post('rate', 'BasicController@rate');
        Route::get('donation_requests', 'DonationRequestsController@index');
        Route::post('change_request_status', 'DonationRequestsController@status');
        Route::get('containers', 'ContainersController@index');
        Route::get('log_dump', 'ContainersController@Logdump');
        Route::post('unload_container', 'ContainersController@unload_container');
        Route::post('update_location','UserController@updateLocation');
    });
});
