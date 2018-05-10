<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Basic;
use Auth;
use App\Models\Setting;
use App\Models\SettingTranslation;
use App\Models\Category;
use App\Models\Game;

class FrontController extends Controller {

    use Basic;

    protected $lang_code;
    protected $User = false;
    protected $isUser = false;
    protected $_Request = false;
    protected $limit = 1;
    protected $order_minutes_limit = 16;
    protected $_settings;
    protected $data = array();

    public function __construct() {
        if (Auth::guard('web')->user() != null) {
            $this->User = Auth::guard('web')->user();
            $this->isUser = true;
        }
        $this->data['User'] = $this->User;
        $this->data['isUser'] = $this->isUser;
        $segment2 = \Request::segment(2);
        $this->data['page_link_name'] = $segment2;


        $this->getLangCode();
        $this->getSettings();
        $this->getCategories();
        $this->data['page_title'] = '';
       
    }

    private function getLangCode() {
        $this->lang_code = app()->getLocale();
        $this->data['lang_code'] = $this->lang_code;
        session()->put('lang_code', $this->lang_code);
        if ($this->data['lang_code'] == 'ar') {
            $this->data['next_lang_code'] = 'en';
            $this->data['next_lang_text'] = 'English';
            $this->data['currency_sign'] = 'جنيه';
        } else {
            $this->data['next_lang_code'] = 'ar';
            $this->data['next_lang_text'] = 'العربية';
            $this->data['currency_sign'] = 'EGP';
        }
        $this->slugsCreate();
    }

    protected function iniDiffLocations($tableName, $lat, $lng) {
        $diffLocations = "SQRT(POW(69.1 * ($tableName.lat - {$lat}), 2) + POW(69.1 * ({$lng} - $tableName.lng) * COS($tableName.lat / 57.3), 2)) as distance";
        return $diffLocations;
    }

    private function getSettings() {
        $this->data['settings'] = Setting::get()->keyBy('name');
        $this->_settings = $this->data['settings'];
        $this->data['settings']['social_media'] = json_decode($this->data['settings']['social_media']->value);
        $this->data['settings']['store'] = json_decode($this->data['settings']['store']->value);
        $this->data['settings_translations'] = SettingTranslation::where('locale', $this->lang_code)->first();
        //dd($this->data['settings']);
    }

    private function getCategories() {
        $this->data['others'] = Category::Join('categories_translations','categories.id','=','categories_translations.category_id')->where('categories_translations.locale',$this->lang_code)->where('categories.active',true)->where('categories.parent_id',0)->orderBy('categories.this_order')->select('categories.slug','categories_translations.title')->get(); 
    }

    protected function _view($main_content, $type = 'front') {
        $main_content = "main_content/$type/$main_content";
        //dd($main_content);
        return view($main_content, $this->data);
    }

    protected function err404($code = false, $message = false) {
        if (!$message) {
            $message = _lang('app.page_not_found');
        }
        if (!$code) {
            $code = 404;
        }
        $this->data['code'] = $code;
        $this->data['message'] = $message;
        return $this->_view('err404');
    }

}
