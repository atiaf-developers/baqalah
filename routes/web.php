<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */


$languages = array('ar', 'en', 'fr');
$defaultLanguage = 'ar';
if ($defaultLanguage) {
    $defaultLanguageCode = $defaultLanguage;
} else {
    $defaultLanguageCode = 'ar';
}

$currentLanguageCode = Request::segment(1, $defaultLanguageCode);
if (in_array($currentLanguageCode, $languages)) {
    Route::get('/', function () use($defaultLanguageCode) {
        return redirect()->to($defaultLanguageCode);
    });


    Route::group(['namespace' => 'Front', 'prefix' => $currentLanguageCode], function () use($currentLanguageCode) {
        app()->setLocale($currentLanguageCode);
        app()->setLocale($currentLanguageCode);
        Route::get('/', 'HomeController@index')->name('home');
        Route::get('getRegionByCity/{id}', 'AjaxController@getRegionByCity');
        Route::get('getAddress/{id}', 'AjaxController@getAddress');
        Route::get('ajax/checkAvailability', 'AjaxController@checkAvailability');
        Route::post('ajax/reserve_submit', 'AjaxController@reserve_submit');
        Auth::routes();

        Route::get('user-activation-code', 'Auth\RegisterController@showActivationForm')->name('activation');
        Route::post('activateuser', 'Auth\RegisterController@activate_user')->name('activationuser');

        Route::get('edit-user-phone', 'Auth\RegisterController@showEditMobileForm')->name('edit-phone');
        Route::post('edituserphone', 'Auth\RegisterController@EditPhone')->name('editphone');

        Route::get('about-us', 'StaticController@about_us')->name('about_us');


        Route::get('news-and-events', 'NewsController@index')->name('news_events');
        Route::get('news-and-events/{slug}', 'NewsController@show')->name('show_news');

        Route::get('corporation-activities', 'ActivitiesController@index')->name('corporation_activities');
        Route::get('corporation-activities/{slug}', 'ActivitiesController@show')->name('show_corporation_activities');

        Route::get('gallary', 'AlbumsController@index')->name('gallary');
        Route::get('gallary/{slug}', 'AlbumsController@show')->name('show_gallary');

        Route::get('video-gallary', 'VideosController@index')->name('video_gallary');



        Route::get('news-and-events', 'NewsController@index')->name('news_events');
        Route::get('news-and-events/{slug}', 'NewsController@show')->name('show_news');

        Route::get('corporation-activities', 'ActivitiesController@index')->name('corporation_activities');
        Route::get('corporation-activities/{slug}', 'ActivitiesController@show')->name('show_corporation_activities');

        Route::get('gallary', 'AlbumsController@index')->name('gallary');
        Route::get('gallary/{slug}', 'AlbumsController@show')->name('show_gallary');

        Route::get('video-gallary', 'VideosController@index')->name('video_gallary');


        Route::get('others/{slug}', 'OthersController@index')->name('others');
        Route::get('others/{section}/{slug}', 'OthersController@show')->name('show_others');

        Route::get('donation-request', 'DonationRequestsController@showDonationRequestForm');
        Route::post('donation-request', 'DonationRequestsController@submitDonationRequestForm');
        Route::post('user/edit', 'UserController@edit');
        Route::post('contact-us', 'StaticController@sendContactMessage');


        /*         * ************************* user ************** */
        Route::group(['namespace' => 'Customer', 'prefix' => 'customer'], function () {
            Route::get('dashboard', 'DashboardController@index');
            Route::get('user/edit', 'UserController@showEditForm');
            Route::post('user/edit', 'UserController@edit');
            Route::get('user/notifications', 'UserController@notifications');
        });
    });
} else {
    Route::get('/' . $currentLanguageCode, function () use($defaultLanguageCode) {
        return redirect()->to($defaultLanguageCode);
    });
}


//Route::group(['middleware'=>'auth:admin'], function () {
Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {
    Route::get('/', 'AdminController@index')->name('admin.dashboard');
    Route::get('/error', 'AdminController@error')->name('admin.error');
    Route::get('/change_lang', 'AjaxController@change_lang')->name('ajax.change_lang');

    Route::get('profile', 'ProfileController@index');
    Route::patch('profile', 'ProfileController@update');



    Route::resource('groups', 'GroupsController');
    Route::resource('admins', 'AdminsController');

    Route::resource('donation_types', 'DonationTypesController');
    Route::resource('donation_requests', 'DonationRequestsController');
    Route::resource('delegates_report', 'DelegatesReportController');
    Route::post('donation_requests/assigned', 'DonationRequestsController@assigned');
    Route::resource('albums', 'AlbumsController');
    Route::resource('album_images', 'AlbumImagesController');
    Route::resource('activities', 'ActivitiesController');
    Route::post('pilgrims/import', 'PilgrimsController@import');
    Route::post('pilgrims/data', 'PilgrimsController@data');
    Route::resource('locations', 'LocationsController');
    Route::resource('our_locations', 'OurLocationsController');
    Route::resource('categories', 'CategoriesController');
    Route::resource('news', 'NewsController');
    Route::resource('videos', 'VideosController');

    Route::get('settings', 'SettingsController@index');

    // Route For Container Moduel {Start}
    Route::resource('container', 'ContainersController');
    Route::post('container/data', 'ContainersController@data');
    // Route For Container Moduel {End}
    // Route For Users Moduel {Start}
    Route::resource('users', 'UsersController');
    Route::post('users/data', 'UsersController@data');
    // Route For Users Moduel {End}




    Route::post('settings', 'SettingsController@store');
    Route::get('notifications', 'NotificationsController@index');
    Route::get('reservations', 'ReservationsController@index');
    Route::post('notifications', 'NotificationsController@store');




    Route::post('groups/data', 'GroupsController@data');
    Route::post('locations/data', 'LocationsController@data');






    Route::post('admins/data', 'AdminsController@data');

    Route::resource('contact_messages', 'ContactMessagesController');
    Route::post('contact_messages/data', 'ContactMessagesController@data');

    Route::post('categories/data', 'CategoriesController@data');
    Route::post('videos/data', 'VideosController@data');
    Route::post('donation_types/data', 'DonationTypesController@data');
    Route::post('albums/data', 'AlbumsController@data');
    Route::post('donation_requests/data', 'AlbumsController@data');
    Route::post('activities/data', 'ActivitiesController@data');
    Route::post('news/data', 'NewsController@data');
    Route::post('album_images/data', 'AlbumImagesController@data');









    $this->get('login', 'LoginController@showLoginForm')->name('admin.login');
    $this->post('login', 'LoginController@login')->name('admin.login.submit');
    $this->get('logout', 'LoginController@logout')->name('admin.logout');
});
//});

