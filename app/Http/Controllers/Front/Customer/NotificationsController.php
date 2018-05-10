<?php

namespace App\Http\Controllers\Front\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class NotificationsController extends FrontController {

    private $edit_rules = array(
    );

    public function __construct() {
        parent::__construct();
    }

    public function index(Request $request) {

        $where_array = array();

        $notifier_id =  $this->User->id;
        $where_array['notifier_id'] =$notifier_id;
        $where_array['notifiable_type'] = 1;
        //$this->notiMarkAsReadByNotifier($notifier_id, $notifiable_type, 1);
        $noti = $this->getNoti($where_array);
        dd($noti);
        return _api_json($this->handleFormateNoti($noti));
    }

    private function handleFormateNoti($noti) {
        $result = array();
        if ($noti->count() > 0) {

            foreach ($noti as $one) {

                $obj = new \stdClass();
                $obj->noti_id = $one->id;
                $obj->id = $one->entity_id;
                $obj->title = '';
                $obj->type = $one->entity_type_id;
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
                    $message = _lang('app.new_activity') . ' ' . $activity->title;
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
                    $message = _lang('app.new_news') . ' ' . $news->title;
                } else {
                    $message = _lang('app.' . Noti::$status_text[$one->entity_type_id]);
                }
                $obj->body = $message;
                $result[] = $obj;
            }
        }
        return $result;
    }

    private function getNoti($where_array) {

        $notifications = DB::table('noti_object as n_o')->join('noti as n', 'n.noti_object_id', '=', 'n_o.id');
        $notifications->select('n.id', 'n_o.entity_id', 'n_o.entity_type_id', 'n.notifier_id', 'n_o.created_at', 'n.read_status');

        $notifications->where(function ($query) use($where_array) {
            $query->where(function ($query2) {
                $query2->whereNull('n.notifier_id');
            });
            $query->orWhere(function ($query2) use($where_array) {
                $query2->where('n.notifier_id', $where_array['user_id']);
                $query2->where('n_o.notifiable_type',$where_array['notifiable_type']);
            });
        });
        $notifications->orderBy('n_o.created_at', 'DESC');
        $result = $notifications->get();
        $result = $notifications->paginate($this->limit);


        return $result;
    }

}
