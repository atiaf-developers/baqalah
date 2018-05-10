<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\City;
use App\Models\Resturant;
use App\Models\Topping;
use App\Models\Size;
use App\Models\User;
use App\Models\MenuSection;
use App\Notifications\GeneralNotification;
use App\Helpers\Fcm;
use App\Mail\GeneralMail;
use Mail;
use Validator;
use Notification;

class AjaxController extends BackendController {

    private $notify_rules = array(
        'title' => 'required',
        'message' => 'required',
    );
    private $email_rules = array(
        'message' => 'required'
    );

    public function change_lang(Request $request) {
        //dd('here');
        $lang_code = $request->input('lang_code');
        //dd($lang_code);
        $long = 7 * 60 * 24;
        return response()->json([
                    'type' => 'success',
                    'message' => $lang_code
                ])->cookie('AdminLang', $lang_code, $long);
    }

    public function getMenueSectionsByResturant($resturant_id)
    {
        $menu_sections = MenuSection::where('resturant_id', $resturant_id)
                       ->where('active', 1)
                       ->select('id', 'title_' . $this->lang_code . ' as title')
                       ->get();

        return _json('success', $menu_sections->toArray());
    }

    public function getRegionByCity($city_id) {
      
        $regions = City::where('parent_id', $city_id)
                       ->where('active', 1)
                       ->select('id', 'title_' . $this->lang_code . ' as title')
                       ->get();

        return _json('success', $regions->toArray());
    }

    public function getToppings()
    {
        $toppings = Topping::where('active',true)->orderBy('this_order')->select('id','title_'.$this->lang_code.' as title')->get();
       $data = "";
        foreach ($toppings as $topping) {
            $data .= "<option  value='".$topping->id."'>".$topping->title."</option>";          
        }
        return $data;
    }

    public function getSizes()
    {
        $sizes = Size::where('active',true)->orderBy('this_order')->select('id','title_'.$this->lang_code.' as title')->get();
       $data = "";
        foreach ($sizes as $size) {
            $data .= "<option  value='".$size->id."'>".$size->title."</option>";          
        }
        return $data;
    }

    public function notify(Request $request) {

        $validator = Validator::make($request->all(), $this->notify_rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {
            //dd($request->input('ids'));
            try {
                $data['title'] = $request->input('title');
                $data['message'] = $request->input('message');
                $token = User::whereIn('id', json_decode($request->input('ids')))->pluck('device_token');
                $users = User::whereIn('id', json_decode($request->input('ids')))->get();
                if ($users->count() > 0) {
                    if ($users->count() <= 30) {
                        Notification::send($users, new GeneralNotification($data));
                        $notification = array('title' => $data['title'], 'body' => $data['message'], 'type' => 0); //new main stadium
                        $Fcm = new Fcm;
                        $Fcm->send($token, $notification, 'and');
                        $Fcm->send($token, $notification, 'ios');
                    }else{
                        return _json('error', _lang('app.maximum_uses_selected_is_30'), 400);
                    }
                } else {
                    return _json('error', _lang('app.no_user_selcted'), 400);
                }

                return _json('success', _lang('app.sending_successfully'));
            } catch (Exception $ex) {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }
    public function send_email(Request $request) {

        $validator = Validator::make($request->all(), $this->email_rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        } else {
            $user_id=$request->input('send_email_user_id');
            $user=User::find($user_id);
            try {
                $data['message'] = $request->input('message');
                Mail::to($user->email)->send(new GeneralMail($data));
                return _json('success', _lang('app.sending_successfully'));
            } catch (Exception $ex) {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

}
