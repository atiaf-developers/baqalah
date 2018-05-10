<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use App\Models\Pages;
use App\Traits\Basic;
use Auth;
use Image;

class BackendController extends Controller {

    use Basic;

    protected $lang_code = 'en';
    protected $User;
    protected $data = array();
    protected $status_text = [
             0 => 'pending',
             1 => 'order_processing_is_ongoing',
             2 => 'order_is_being_delivered',
             3 => 'order_was_deliverd',
             4 => 'order_was_rejected',
    ];

    public function __construct() {
        $this->middleware('auth:admin');
        $segment2 = \Request::segment(2);
        $this->data['page_link_name'] = $segment2;
        $this->User = Auth::guard('admin')->user();
        $this->data['User'] = $this->User;
        $this->data['languages'] = $this->languages;
        $this->getCookieLangAndSetLocale();
        $pages = Pages::getPages();
        $this->data['pages'] = $this->sideBarHtml($pages);
        $this->slugsCreate();
    }

    protected function getCookieLangAndSetLocale() {
        if (\Cookie::get('AdminLang') !== null) {
            try {
                $this->lang_code = \Crypt::decrypt(\Cookie::get('AdminLang'));
            } catch (DecryptException $ex) {
                $this->lang_code = 'en';
            }
        } else {
            $this->lang_code = 'en';
        }
       
        $this->data['lang_code'] = $this->lang_code;
        if ($this->lang_code == "ar") {
            $this->data['currency_sign'] = 'ريال'; 
        }
        else{
            $this->data['currency_sign'] = 'SAR';
        }
       
        app()->setLocale($this->lang_code);
    }

    public function sideBarHtml($pages) {
        $markup = "";
        $page_link_name = $this->data['page_link_name'];
        $page_arr = Pages::where('controller', $page_link_name)->get();
        $page_parents = (count($page_arr)) ? explode(',', $page_arr[0]->parents_ids) : array();
//        dd($page_parents);
//        $ids = array();
        foreach ($pages as $page) {
            $parentClass = '';
            $style = '';
            if (\Permissions::check($page->name, 'open')) {

                if ($page_link_name === null) {
                    if ($page->name == 'dashboard') {
                        $parentClass = 'active';
                    }
                } else {

                    if (in_array($page->id, $page_parents)) {
                        $style = 'style="display:block;"';
                        $parentClass = 'active';
                    }
                    if (count($page_arr)) {
                        if ($page_arr[0]->id == $page->id) {
                            $parentClass = 'active';
                        }
                    }
                }

//                if (in_array($page->id, $ids)) {
//                    $style = 'style="display:block;"';
//                    $parentClass = 'active';
//                }
                $markup .= '<li class="nav-item start ' . $parentClass . '">';
                $url = (!empty($page->children)) ? 'javascript:;' : url("admin/$page->controller");
                $markup .= '<a href="' . $url . '" class="nav-link nav-toggle">';
                $markup .= '<i class="icon-home"></i>';
                $markup .= '<span class="title">' . _lang("app.$page->name") . '</span>';
                if (isset($page->children) && !empty($page->children)) {
                    $markup .= ' <span class="arrow"></span>';
                }
                $markup .= ' </a>';
                if (isset($page->children) && !empty($page->children)) {
                    $markup .= '<ul class="sub-menu" ' . $style . '>';
                    //$this->sideBarHtml($page->children);
                    $markup .= $this->sideBarHtml($page->children);
                    //dd($markup);
                    $markup .= '</ul>';
                }
                //dd($markup);
                $markup .= '</li>';
            }
        }

        return $markup;
    }

 

    

    public function err404() {
        return view('main_content/backend/err404');
    }

}
