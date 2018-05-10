<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\AdminNotification;
use App\Models\Device;
use App\Models\Noti;
use App\Models\News;
use App\Models\Activity;
use App\Models\DonationRequest;
use DB;

class NotificationsController extends ApiController {

    public function __construct() {
        parent::__construct();
    }

    public function index2(Request $request) {
        $user = $this->auth_user();
        if (!$user) {
            $rules['device_id'] = 'required';
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json([], ['errors' => $errors], 400);
            }
        }

        $where_array = array();

        if ($user) {
            $where_array['user_id'] = $user->id;
            $notifier_id = $user->id;
            $notifiable_type = 1;
        } else {
            $device = Device::where('device_id', $request->input('device_id'))->first();
            $device_id = $device ? $device->id : null;
            $where_array['device_id'] = $device_id;
            $notifier_id = $device->id;
            $notifiable_type = 3;
        }
        $this->notiMarkAsReadByNotifier($notifier_id, $notifiable_type, 1);
        $noti = $this->getNoti($where_array);
        return _api_json($this->handleFormateNoti($noti));
    }
    public function index() {
        $user = $this->auth_user();
        $where_array['notifier_id'] = $user->id;
        $where_array['notifiable_type'] = 1;
        $noti = Noti::getNoti($where_array,'ForApi');
          return _api_json($noti);
    }

    private function getNoti($where_array) {

        $notifications = DB::table('noti_object as n_o')->join('noti as n', 'n.noti_object_id', '=', 'n_o.id');
        $notifications->select('n.id', 'n_o.entity_id', 'n_o.entity_type_id', 'n.notifier_id', 'n_o.created_at', 'n.read_status');

        $notifications->where(function ($query) use($where_array) {
            $query->where(function ($query2) {
                $query2->whereNull('n.notifier_id');
            });
            $query->orWhere(function ($query2) use($where_array) {
                if (isset($where_array['user_id'])) {
                    $query2->where('n.notifier_id', $where_array['user_id']);
                    $query2->where('n_o.notifiable_type', 1);
                } else if (isset($where_array['device_id'])) {
                    $query2->where('n.notifier_id', $where_array['device_id']);
                    $query2->where('n_o.notifiable_type', 3);
                }
            });
        });
        $notifications->orderBy('n_o.created_at', 'DESC');
        $result = $notifications->get();
        $result = $notifications->paginate($this->limit);


        return $result;
    }

    private function getNoti2($where_array) {

        $notifications = DB::table('noti_object as n_o')->join('noti as n', 'n.noti_object_id', '=', 'n_o.id');
        $notifications->select('n.id', 'n_o.entity_id', 'n_o.entity_type_id', 'n.notifier_id', 'n_o.created_at', 'n.read_status');

        if (isset($where_array['user_id'])) {
            $notifications->where('n.notifier_id', $where_array['user_id']);
            $notifications->where('n_o.notifiable_type', 1);
        } else if (isset($where_array['device_id'])) {
            $notifications->where('n.notifier_id', $where_array['device_id']);
            $notifications->where('n_o.notifiable_type', 3);
        }
        $notifications->orWhere('n.notifier_id', null);
        $notifications->orderBy('n_o.created_at', 'DESC');
        $result = $notifications->get();
        $result = $notifications->paginate($this->limit);


        return $result;
    }

    private function handleFormateNoti($noti) {
        $result = array();
        if ($noti->count() > 0) {

            foreach ($noti as $one) {
                 if (in_array($one->entity_type_id,[2,3,4])) {
                     $type = 1;
                 }
                 else if($one->entity_type_id == 5){
                     $type = 2;
                 }else{
                      $type = 3;
                 }
                $obj = new \stdClass();
                $obj->noti_id = $one->id;
                $obj->id = $one->entity_id;
                $obj->title = '';
                $obj->type = $type;
                $obj->created_at = date('d/m/Y   g:i A', strtotime($one->created_at));
                $obj->read_status = $one->read_status;

                if ($one->entity_type_id == 5) {
                    $activity = Activity::Join('activities_translations', 'activities.id', '=', 'activities_translations.activity_id')
                            ->where('activities_translations.locale', $this->lang_code)
                            ->where('activities.active', true)
                            ->where('activities.id', $one->entity_id)
                            ->select("activities_translations.title")
                            ->first();
                    if (!$activity) {
                        continue;
                    }
                    $message = _lang('app.new_activity') . ' \n ' . $activity->title;
                } else if ($one->entity_type_id == 6) {
                    $news = News::Join('news_translations', 'news.id', '=', 'news_translations.news_id')
                            ->where('news_translations.locale', $this->lang_code)
                            ->where('news.active', true)
                            ->where('news.id', $one->entity_id)
                            ->select('news_translations.title')
                            ->first();
                    if (!$news) {
                        continue;
                    }
                    $message = _lang('app.new_news') . ' \n ' . $news->title;
                } else {
                    $donation_request = DonationRequest::join('donation_types','donation_types.id','=','donation_requests.donation_type_id')
                    ->join('donation_types_translations','donation_types.id','=','donation_types_translations.donation_type_id')
                    ->where('donation_types_translations.locale',$this->lang_code)
                    ->where('donation_requests.id',$one->entity_id)
                    ->select('donation_types_translations.title','donation_requests.description')
                    ->first();
                    $message = _lang('app.' . Noti::$status_text[$one->entity_type_id]).'\n'._lang('app.donation_type').' : '.$donation_request->title.'\n'._lang('app.detailes').' : '.$donation_request->description;
                }
                $obj->body = $message;
                $result[] = $obj;
            }
        }
        return $result;
    }

    private function notiMarkAsReadByNotifier($notifier_id, $notifiable_type, $read_status) {
        $sql = "UPDATE noti_object n_o 
                JOIN noti n ON n_o.id = n.noti_object_id And n.read_status=0 And n.notifier_id=$notifier_id And n_o.notifiable_type=$notifiable_type             
                SET n.read_status = $read_status";
        DB::statement($sql);
    }
    
    public function getUnReadNoti(Request $request) {
        $user = $this->auth_user();
        if (!$user) {
            $rules['device_id'] = 'required';
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json([], ['errors' => $errors], 400);
            }
        }


        $notifications = DB::table('noti_object as n_o')->join('noti as n', 'n.noti_object_id', '=', 'n_o.id');
        if ($user) {
            $notifications->where('n.notifier_id', $user->id);
            $notifications->where('n_o.notifiable_type', 1);
        } else {
            $device = Device::where('device_id', $request->input('device_id'))->first();
            $device_id = $device ? $device->id : null;
            $notifications->where('n.notifier_id', $device_id);
            $notifications->where('n_o.notifiable_type', 3);
        }
        $notifications->where('n.read_status', 0);
        return $notifications->count();
    }

}
