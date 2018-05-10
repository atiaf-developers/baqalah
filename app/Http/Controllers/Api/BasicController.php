<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Setting;
use App\Models\SettingTranslation;
use App\Models\Category;
use App\Models\News;
use App\Models\DonationType;
use App\Models\ContactMessage;
use App\Models\Activity;
use App\Models\Video;
use App\Models\Device;
use App\Models\Noti;
use App\Models\Album;
use App\Helpers\Fcm;
use Carbon\Carbon;
use DB;

class BasicController extends ApiController {

    private $contact_rules = array(
        'message' => 'required',
        'email' => 'required|email',
        'type' => 'required',
        'name' => 'required'
    );

    public function getToken(Request $request) {
        $token = $request->header('authorization');
        if ($token != null) {
            $token = Authorization::validateToken($token);
            if ($token) {
                $new_token = new \stdClass();
                $find = User::find($token->id);
                if ($find != null) {
                    $new_token->id = $find->id;
                    $new_token->expire = strtotime('+ ' . $this->expire_no . $this->expire_type);
                    $expire_in_seconds = $new_token->expire;
                    return _api_json('', ['token' => AUTHORIZATION::generateToken($new_token), 'expire' => $expire_in_seconds]);
                } else {
                    return _api_json('', ['message' => 'user not found'], 401);
                }
            } else {
                return _api_json('', ['message' => 'invalid token'], 401);
            }
        } else {
            return _api_json('', ['message' => 'token not provided'], 401);
        }
    }

    public function getSettings() {
        try {
            $settings = Setting::select('name', 'value')->get()->keyBy('name');
            $settings['social_media'] = json_decode($settings['social_media']->value);
            $settings['info'] = SettingTranslation::where('locale', $this->lang_code)->first();

            return _api_json($settings);
        } catch (\Exception $e) {
            return _api_json(new \stdClass(), ['message' => $e->getMessage()], 400);
        }
    }

    public function sendContactMessage(Request $request) {
        $validator = Validator::make($request->all(), $this->contact_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 400);
        } else {
            try {
                $ContactMessage = new ContactMessage;
                $ContactMessage->email = $request->input('email');
                $ContactMessage->type = $request->input('type');
                $ContactMessage->message = $request->input('message');
                $ContactMessage->name = $request->input('name');
                $ContactMessage->save();
                return _api_json('', ['message' => _lang('app.message_is_sent_successfully')]);
            } catch (\Exception $ex) {
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
        }
    }


    public function getCategories() {
        try {
            $categories = Category::Join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
                    ->where('categories_translations.locale', $this->lang_code)
                    ->where('categories.active', true)
                    ->where('categories.parent_id', 0)
                    ->orderBy('categories.this_order')
                    ->select("categories.id", "categories_translations.title","categories.image")
                    ->get();
            return _api_json(Category::transformCollection($categories));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getStoreCategories(Request $request) {
        try {
            $user = $this->auth_user();

            $categories = Category::Join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
                    ->join('store_categories', 'categories.id', '=', 'store_categories.category_id')
                    ->join('stores', 'stores.id', '=', 'store_categories.store_id')
                    ->where('categories_translations.locale', $this->lang_code)
                    ->where('categories.active', true)
                    ->where('stores.user_id', $user->id)
                    ->orderBy('categories.this_order')
                    ->select("categories.id", "categories_translations.title")
                    ->get();

            return _api_json(Category::transformCollection($categories));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getDonationTypes() {
        try {

            $donation_types = DonationType::Join('donation_types_translations', 'donation_types.id', '=', 'donation_types_translations.donation_type_id')
                    ->where('donation_types_translations.locale', $this->lang_code)
                    ->where('donation_types.active', true)
                    ->orderBy('donation_types.this_order')
                    ->select("donation_types.id", "donation_types_translations.title")
                    ->get();

            return _api_json($donation_types);
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getActivities() {
        try {
            $activities = Activity::Join('activities_translations', 'activities.id', '=', 'activities_translations.activity_id')
                    ->where('activities_translations.locale', $this->lang_code)
                    ->where('activities.active', true)
                    ->orderBy('activities.this_order')
                    ->select("activities.id", "activities.images", "activities_translations.title", "activities_translations.description")
                    ->paginate($this->limit);

            return _api_json(Activity::transformCollection($activities));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getVideos() {
        try {
            $videos = Video::Join('videos_translations', 'videos.id', '=', 'videos_translations.video_id')
                    ->where('videos_translations.locale', $this->lang_code)
                    ->where('videos.active', true)
                    ->orderBy('videos.this_order')
                    ->select("videos.id", "videos.youtube_url", "videos_translations.title")
                    ->paginate($this->limit);

            return _api_json(Video::transformCollection($videos));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getAlbums() {
        try {
            $albums = Album::Join('albums_translations', 'albums.id', '=', 'albums_translations.album_id')
                    ->where('albums_translations.locale', $this->lang_code)
                    ->where('albums.active', true)
                    ->orderBy('albums.this_order')
                    ->select("albums.id", "albums_translations.title")
                    ->paginate($this->limit);

            return _api_json(Album::transformCollection($albums));
        } catch (\Exception $e) {

            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getActivityDetailes($id) {
        try {
            $activity = Activity::Join('activities_translations', 'activities.id', '=', 'activities_translations.activity_id')
                    ->where('activities_translations.locale', $this->lang_code)
                    ->where('activities.active', true)
                    ->where('activities.id', $id)
                    ->select("activities.id", "activities.images", "activities_translations.title", "activities_translations.description")
                    ->first();
            if (!$activity) {
                return _api_json(new \stdClass(), ['message' => _lang('app.not_found')], 404);
            }

            return _api_json(Activity::transform($activity));
        } catch (\Exception $e) {
            return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getNewsDetailes($id) {
        try {

            $news = News::Join('news_translations', 'news.id', '=', 'news_translations.news_id')
                    ->where('news_translations.locale', $this->lang_code)
                    ->where('news.active', true)
                    ->where('news.id', $id)
                    ->select('news.id', 'news.images', 'news.created_at', 'news_translations.title', 'news_translations.description')
                    ->first();

            if (!$news) {
                return _api_json(new \stdClass(), ['message' => _lang('app.not_found')], 404);
            }
            return _api_json(News::transform($news));
        } catch (\Exception $e) {
            return _api_json(new \stdClass(), ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    

}
