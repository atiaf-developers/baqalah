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
        'setting.email' => 'required|email', 'setting.phone' => 'required',
        // 'setting.work_from' => 'required', 'setting.work_to' => 'required',
        'setting.social_media.facebook' => 'required',
        'setting.social_media.twitter' => 'required',
        'setting.social_media.instagram' => 'required',
        'setting.social_media.google' => 'required',
        'setting.social_media.youtube' => 'required',
        'setting.store.android' => 'required',
        'setting.store.ios' => 'required',
    );

    public function index() {

        $this->data['settings'] = Setting::get()->keyBy('name');
        // dd($this->data['settings']);
        if($this->data['settings']){
            $this->data['settings']['social_media']=json_decode($this->data['settings']['social_media']->value);
            $this->data['settings']['store']=json_decode($this->data['settings']['store']->value);
        }
        
        //dd($this->data['settings']['social_media']);
        $this->data['settings_translations'] = SettingTranslation::get()->keyBy('locale');
        return $this->_view('settings/index', 'backend');
    }

    public function store(Request $request) {

        if ($request->file('about_image')) {
            $this->rules['about_image'] = 'image|mimes:gif,png,jpeg|max:1000';
        }
        $columns_arr = array(
            // 'title' => 'required',
            'about' => 'required',
            'description' => 'required',
            'address' => 'required',
            'policy' => 'required',
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
                foreach($setting as $key=>$value){
                    if($key=='social_media' || $key=='store'){

                        Setting::updateOrCreate(
                        ['name' => $key], ['value' => json_encode($value)]);
                    }
                   else{
                        Setting::updateOrCreate(
                            ['name' => $key], ['value' => $value]);
                    }
                }
                // $title = $request->input('title');
                $description = $request->input('description');
                $address = $request->input('address');
                $about = $request->input('about');
                $policy = $request->input('policy');
                foreach ($about as $key => $value) {
                    SettingTranslation::updateOrCreate(
                            ['locale' => $key], [
                                'locale' => $key, 'title' => $value, 'description' => $description[$key],
                                'address' => $address[$key], 'about' => $about[$key],'policy' => $policy[$key]
                            ]);
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
