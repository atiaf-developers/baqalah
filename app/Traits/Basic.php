<?php

namespace App\Traits;

use App\Models\Setting;
use Image;
use App\Models\NotiObject;
use App\Models\Noti;
use App\Helpers\Fcm;
use App\Models\Device;
use DB;

trait Basic {

    protected $languages = array(
        'ar' => 'arabic',
        'en' => 'english'
    );

    protected static function getLangCode() {
        $lang_code = app()->getLocale();

        return $lang_code;
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

    public function _view($main_content, $type = 'front') {
        $main_content = "main_content/$type/$main_content";
        return view($main_content, $this->data);
    }

    protected function settings() {
        $settings = Setting::get();
        $settings[0]->noti_status = json_decode($settings[0]->noti_status);
        return $settings[0];
    }

    protected function slugsCreate() {
        $this->title_slug = 'title_' . $this->lang_code;
        $this->data['title_slug'] = $this->title_slug;
    }

    protected function send_noti_fcm($notification, $user_id = false, $device_token = false, $device_type = false) {
        if(!isset($notification['title'])){
            $notification['title']= env('APP_NAME');
        }
        $Fcm = new Fcm;
        if ($user_id) {
            $token_and = Device::whereIn('user_id', $user_id)
                    ->where('device_type', 1)
                    ->pluck('device_token');
            $token_ios = Device::whereIn('user_id', $user_id)
                    ->where('device_type', 2)
                    ->pluck('device_token');
            $token_and = $token_and->toArray();
            $token_ios = $token_ios->toArray();
            if (count($token_and) > 0) {
                //$token_and=$token_and[0];
                //dd($token_and);
                return $Fcm->send($token_and, $notification, 'and');
            } else if (count($token_ios) > 0) {
                return $Fcm->send($token_ios, $notification, 'ios');
            }
        } else {
            $device_type = $device_type == 1 ? 'and' : 'ios';
            return $Fcm->send($device_token, $notification, $device_type);
        }
    }

    /* public function updateValues($model, $data) {
      //dd($values);
      $table = $model::getModel()->getTable();
      //dd($table);
      $cases = [];
      $ids = [];
      $sql_arr = [];
      $columns = array_keys($data);
      foreach ($data as $one) {
      $id = (int) $one['id'];
      $cases[] = "WHEN {$id} then {$one['value']}";
      $ids[] = $id;
      }
      $ids = implode(',', $ids);
      $cases = implode(' ', $cases);
      foreach ($columns as $column) {
      $sql_arr[] = "SET `{$column}` = CASE `id` {$cases} END";
      }
      $sql_str = implode(',', $sql_arr);
      //dd($sql_str);
      //$params[] = Carbon::now();
      //return DB::update("UPDATE `$table` SET `remaining_available_of_accommodation` = CASE `id` {$cases} END WHERE `id` in ({$ids})");
      return DB::update("UPDATE `$table` $sql_str WHERE `id` in ({$ids})");
      } */

    protected function create_noti($entity_id, $notifier_id, $entity_type, $notifible_type = 1) {
        $NotiObject = new NotiObject;
        $NotiObject->entity_id = $entity_id;
        $NotiObject->entity_type_id = $entity_type;
        $NotiObject->notifiable_type = $notifible_type;
        $NotiObject->save();
        $Noti = new Noti;
        $Noti->notifier_id = $notifier_id;
        $Noti->noti_object_id = $NotiObject->id;

        $Noti->save();
    }

    protected function lang_rules($columns_arr = array()) {
        $rules = array();

        if (!empty($columns_arr)) {
            foreach ($columns_arr as $column => $rule) {
                foreach ($this->languages as $lang_key => $locale) {
                    $key = $column . '.' . $lang_key;
                    $rules[$key] = $rule;
                }
            }
        }
        return $rules;
    }

    public function updateValues($model, $data) {
        //dd($values);
        $table = $model::getModel()->getTable();
        //dd($table);

        $columns = array_keys($data);

        $ids = [];
        $sql_arr = [];
        foreach ($data as $column => $value_arr) {
            $cases = [];
            foreach ($value_arr as $one) {
                $id = (int) $one['id'];
                $cases[] = "WHEN {$id} then {$one['value']}";
                $ids[] = $id;
            }
            //dd($cases);
            $cases = implode(' ', $cases);
            $sql_arr[] = "SET `{$column}` = CASE `id` {$cases} END";
        }
        $ids = implode(',', $ids);
        $sql_str = implode(',', $sql_arr);
        //dd($sql_str);
        //$params[] = Carbon::now();
        //return DB::update("UPDATE `$table` SET `remaining_available_of_accommodation` = CASE `id` {$cases} END WHERE `id` in ({$ids})");
        return DB::update("UPDATE `$table` $sql_str WHERE `id` in ({$ids})");
    }

    public function updateValues2($model, $data) {
        //dd($values);
        $table = $model::getModel()->getTable();
        //dd($table);
        $cases = [];
        $ids = [];
        $sql_arr = [];
        $columns = array_keys($data);
        foreach ($data as $one) {
            $id = (int) $one['id'];
            $cases[] = "WHEN {$id} then {$one['value']}";
            $ids[] = $id;
        }
        $ids = implode(',', $ids);
        $cases = implode(' ', $cases);
        foreach ($columns as $column) {
            $sql_arr[] = "SET `{$column}` = CASE `id` {$cases} END";
        }
        $sql_str = implode(',', $sql_arr);
        //dd($sql_str);
        //$params[] = Carbon::now();
        //return DB::update("UPDATE `$table` SET `remaining_available_of_accommodation` = CASE `id` {$cases} END WHERE `id` in ({$ids})");
        return DB::update("UPDATE `$table` $sql_str WHERE `id` in ({$ids})");
    }

}
