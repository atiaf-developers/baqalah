<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Noti extends MyModel {

    protected $table = 'noti';


    public static function transformForApi($item) {
        $lang_code = static::getLangCode();
        $obj = new \stdClass();
        if (in_array($item->entity_type_id, [2, 3, 4])) {
            $type = 1;
        } else if ($item->entity_type_id == 5) {
            $type = 2;
        } else {
            $type = 3;
        }
        $obj->noti_id = $item->id;
        $obj->id = $item->entity_id;
        $obj->title = '';
        $obj->type = $type;
        $obj->created_at = date('d/m/Y   g:i A', strtotime($item->created_at));
        $obj->read_status = $item->read_status;
        $message = '';
        if ($item->entity_type_id == 5) {
            $activity = Activity::Join('activities_translations', 'activities.id', '=', 'activities_translations.activity_id')
                    ->where('activities_translations.locale', $lang_code)
                    ->where('activities.active', true)
                    ->where('activities.id', $item->entity_id)
                    ->select("activities.slug", "activities_translations.title")
                    ->first();
            if ($activity) {
                $message = _lang('app.new_activity') . ' \n ' . $activity->title;
            }
        } else if ($item->entity_type_id == 6) {
            $news = News::Join('news_translations', 'news.id', '=', 'news_translations.news_id')
                    ->where('news_translations.locale', $lang_code)
                    ->where('news.active', true)
                    ->where('news.id', $item->entity_id)
                    ->select("news.slug", 'news_translations.title')
                    ->first();
            if ($news) {
                $message = _lang('app.new_news') . ' \n ' . $news->title;
            }
        } else {
            $donation_request = DonationRequest::join('donation_types', 'donation_types.id', '=', 'donation_requests.donation_type_id')
                    ->join('donation_types_translations', 'donation_types.id', '=', 'donation_types_translations.donation_type_id')
                    ->where('donation_types_translations.locale', $lang_code)
                    ->where('donation_requests.id', $item->entity_id)
                    ->select('donation_types_translations.title', 'donation_requests.description')
                    ->first();
            $status_text = DonationRequest::$status_text[$item->entity_type_id]['client']['message_' . $lang_code];
            $message = $status_text . '\n' . _lang('app.donation_type') . ' : ' . $donation_request->title . '\n' . _lang('app.detailes') . ' : ' . $donation_request->description;
        }
        $obj->body = $message;

        return $obj;
    }

    public static function transformForFront($item) {
        $lang_code = static::getLangCode();
        $obj = new \stdClass();
        if (in_array($item->entity_type_id, [2, 3, 4])) {
            $type = 1;
        } else if ($item->entity_type_id == 5) {
            $type = 2;
        } else {
            $type = 3;
        }
        $obj->noti_id = $item->id;
        $obj->id = $item->entity_id;
        $obj->title = '';
        $obj->type = $type;
        $obj->created_at = date('d/m/Y   g:i A', strtotime($item->created_at));
        $obj->read_status = $item->read_status;
        $message = '';
        $url = '';
        if ($item->entity_type_id == 5) {
            $activity = Activity::Join('activities_translations', 'activities.id', '=', 'activities_translations.activity_id')
                    ->where('activities_translations.locale', $lang_code)
                    ->where('activities.active', true)
                    ->where('activities.id', $item->entity_id)
                    ->select("activities.slug", "activities_translations.title")
                    ->first();
            if ($activity) {
                $url = _url('corporation-activities/' . $activity->slug);
                $message = _lang('app.new_activity') . '<br>' . $activity->title;
            }
        } else if ($item->entity_type_id == 6) {
            $news = News::Join('news_translations', 'news.id', '=', 'news_translations.news_id')
                    ->where('news_translations.locale', $lang_code)
                    ->where('news.active', true)
                    ->where('news.id', $item->entity_id)
                    ->select("news.slug", 'news_translations.title')
                    ->first();
            if ($news) {
                $message = _lang('app.new_news') . '<br>' . $news->title;
                $url = _url('news-and-events/' . $news->slug);
            }
        } else {
            $donation_request = DonationRequest::join('donation_types', 'donation_types.id', '=', 'donation_requests.donation_type_id')
                    ->join('donation_types_translations', 'donation_types.id', '=', 'donation_types_translations.donation_type_id')
                    ->where('donation_types_translations.locale', $lang_code)
                    ->where('donation_requests.id', $item->entity_id)
                    ->select('donation_types_translations.title', 'donation_requests.description')
                    ->first();
            $status_text = DonationRequest::$status_text[$item->entity_type_id]['client']['message_' . $lang_code];
            $message = $status_text . '<br>' . _lang('app.donation_type') . ' : ' . $donation_request->title . '<br>' . $donation_request->description;
        }
        $obj->body = $message;
        $obj->url = $url;

        return $obj;
    }

    public static function getNoti($where_array, $transform_type = 'ForApi') {

        $notifications = DB::table('noti_object as n_o')->join('noti as n', 'n.noti_object_id', '=', 'n_o.id');
        $notifications->select('n.id', 'n_o.entity_id', 'n_o.entity_type_id', 'n.notifier_id', 'n_o.created_at', 'n.read_status');

        $notifications->where(function ($query) use($where_array) {
            $query->where(function ($query2) {
                $query2->whereNull('n.notifier_id');
            });
            $query->orWhere(function ($query2) use($where_array) {
                $query2->where('n.notifier_id', $where_array['notifier_id']);
                $query2->where('n_o.notifiable_type', $where_array['notifiable_type']);
            });
        });
        $notifications->orderBy('n_o.created_at', 'DESC');
        $result = $notifications->get();
        $result = $notifications->paginate(static::$limit);

        if ($transform_type == 'ForApi') {
            $result = $result->getCollection()->transform(function($item, $key) use($transform_type) {
                $transform = 'transform' . $transform_type;
                return static::$transform($item);
            });
        } else {
            $result->getCollection()->transform(function($item, $key) use($transform_type) {
                $transform = 'transform' . $transform_type;
                return static::$transform($item);
            });
        }

        return $result;
    }

}
