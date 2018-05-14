<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\BackendController;
use App\Models\Setting;
use App\Models\SettingTranslation;
use DB;

class SettingsController extends BackendController {

    private $rules = array(
        'setting.search_range_for_stores' => 'required',
        'setting.commission' => 'required',
        'setting.stores_activation' => 'required',
    );

    public function index() {

        $this->data['settings'] = Setting::get()->keyBy('name');
        $this->data['settings_translations'] = SettingTranslation::get()->keyBy('locale');
        return $this->_view('settings/index', 'backend');
    }

    public function store(Request $request) {

       
        $columns_arr = array(
            'about_us' => 'required',
            'usage_conditions' => 'required',
        );
       
        $this->rules = array_merge($this->rules, $this->lang_rules($columns_arr));
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {

            DB::beginTransaction();
            try {
                $setting = $request->input('setting');
                
                foreach($setting as $key => $value){
                    Setting::updateOrCreate(['name' => $key], ['value' => $value]);
                }
               
                $about_us = $request->input('about_us');
                $usage_conditions = $request->input('usage_conditions');
                foreach ($this->languages as $key => $value) {
                    SettingTranslation::updateOrCreate(
                            ['locale' => $key], 
                            [ 'locale' => $key, 'about_us' => $about_us[$key],'usage_conditions' => $usage_conditions[$key] ]
                            );
                }
                DB::commit();
                return _json('success', _lang('app.updated_successfully'));
            } catch (\Exception $ex) {
                DB::rollback();
                dd($ex->getMessage());
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }



}
