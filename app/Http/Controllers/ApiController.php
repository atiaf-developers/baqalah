<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Friendship;
use App\Models\UserBlockPost;
use App\Models\AdminNotification;
use App\Models\Order;
use App\Models\Notification;
use App\Models\Setting;
use App\Traits\Basic;
use App\Models\Category;
use Request;

class ApiController extends Controller {

    use Basic;

    protected $lang_code;
    protected $User;
    protected $data;
    protected $limit = 10;
    protected $expire_no = 1;
    protected $expire_type = 'day';

    public function __construct() {
        $this->getLangAndSetLocale();
        $this->slugsCreate();
    }

  

    private function getLangAndSetLocale() {
        $languages = array('ar', 'en','ur');
        $lang = Request::header('lang');
        if ($lang == null || !in_array($lang, $languages)) {
            $lang = 'ar';
        }
        $this->currency_sign=$lang=='ar'?'ريال':'SAR';
        //return _api_json(false,'ssss');
        $this->lang_code = $lang;
        app()->setLocale($lang);
    }

    protected function inputs_check($model, $inputs = array(), $id = false, $return_errors = true) {
        $errors = array();
        foreach ($inputs as $key => $value) {
            $where_array = array();
            $where_array[] = array($key, '=', $value);
            if ($id) {
                $where_array[] = array('id', '!=', $id);
            }

            $find = $model::where($where_array)->get();

            if (count($find)) {

                $errors[$key] = array(_lang('app.' . $key) . ' ' . _lang("app.added_before"));
            }
        }

        return $errors;
    }

    private function slugsCreate() {

        $this->title_slug = 'title_' . $this->lang_code;
        $this->data['title_slug'] = $this->title_slug;
    }
 
    protected function auth_user() {
        $token = Request::header('authorization');
  
        $token = Authorization::validateToken($token);
        $user = null;
        if ($token) {
            $user = User::find($token->id);
        }

        return $user;
    }

   protected function update_token($device_token, $device_type, $user_id) {
        $User = User::find($user_id);
        $User->device_token = $device_token;
        $User->device_type = $device_type;
        $User->save();
    }
    
    protected function iniDiffLocations($tableName, $lat, $lng)
    {
        $diffLocations = "SQRT(POW(69.1 * ($tableName.lat - {$lat}), 2) + POW(69.1 * ({$lng} - $tableName.lng) * COS($tableName.lat / 57.3), 2)) as distance";
        return $diffLocations;
    }

    
    protected function getGeneralMessages() {
        return [
            'required' => _lang('app.this_field_is_required'),
            'is_base64image' => _lang('app.this_field_must_be_base64_as_image'),
            'onefromjsonarray' => _lang('app.you_should_select_one_at_least'),
        ];
    }

    protected function storeCategories($store_id)
    {

        $categories = Category::Join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
            ->join('store_categories', 'categories.id', '=', 'store_categories.category_id')
            ->join('stores', 'stores.id', '=', 'store_categories.store_id')
            ->where('categories_translations.locale', $this->lang_code)
            ->where('categories.active', true)
            //->where('stores.user_id', $user->id)
            ->where('stores.id', $store_id)
            ->orderBy('categories.this_order')
            ->select("categories.id", "categories_translations.title")
            ->get();

        return $categories;

    }


    protected function settings()
    {
        $settings = Setting::select('name', 'value')->get()->keyBy('name');
        return $settings;
    }

   

}
