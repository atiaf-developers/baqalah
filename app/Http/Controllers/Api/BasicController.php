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
use App\Models\Store;
use App\Models\ContactMessage;
use App\Helpers\Fcm;
use Carbon\Carbon;
use DB;

class BasicController extends ApiController {

    private $contact_rules = array(
        'message' => 'required',
        'email' => 'required|email',
        'subject' => 'required',
        'name' => 'required',
        'store_id' => 'required'
    );

    private $categories_rules = array(
        'lat' => 'required',
        'lng' => 'required'
    );

    private $store_categories_rules = array(
        'store_id' => 'required',
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
            $settings = SettingTranslation::where('locale', $this->lang_code)->first();
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
                $ContactMessage->name = $request->input('name');
                $ContactMessage->email = $request->input('email');
                $ContactMessage->subject = $request->input('subject');
                $ContactMessage->message = $request->input('message');
                $ContactMessage->store_id = $request->input('store_id');
                $ContactMessage->save();
                return _api_json('', ['message' => _lang('app.message_is_sent_successfully')]);
            } catch (\Exception $ex) {
                return _api_json('', ['message' => _lang('app.error_is_occured')], 400);
            }
        }
    }

    public function getComplaints(Request $request) {
        $validator = Validator::make($request->all(), ['store_id' => 'required']);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json([], ['errors' => $errors], 400);
        } 
        try {
            $complaints = ContactMessage::where('store_id',$request->input('store_id'))
            ->select('name','email','subject','message')
            ->paginate($this->limit);
            return _api_json(ContactMessage::transformCollection($complaints));
        } catch (\Exception $ex) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
        
    }


    public function getCategories(Request $request) {
        try {
            $user = $this->auth_user();
            if ($user) {
                $validator = Validator::make($request->all(), $this->categories_rules);
                if ($validator->fails()) {
                    $errors = $validator->errors()->toArray();
                    return _api_json('', ['errors' => $errors], 400);
                }
            }
            $categories = $this->categories($request,$user);
            return $categories;
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }

    public function getStoreCategories(Request $request) {
        try {
            $validator = Validator::make($request->all(), $this->store_categories_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            }
            $categories = $this->storeCategories($request->input('store_id'));
            return _api_json(Category::transformCollection($categories));
        } catch (\Exception $e) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
    }


    private function categories($request,$user = null)
    {
        $settings = $this->settings();
        $distance = $settings['search_range_for_stores']->value;
        $columns = ["categories.id", "categories_translations.title"];
        
        if ($user) {
            $columns[] = "categories.image";
            $lat = $request->input('lat');
            $lng = $request->input('lng');

            $store = Store::leftJoin('ratings', function ($join) use($user) {
                $join->on('ratings.store_id', '=', 'stores.id');
                $join->where('ratings.user_id',  $user->id);
            })
            ->where('stores.active',true)
            ->select(['stores.id','stores.name','stores.description','stores.image','stores.mobile','stores.lat','stores.lng','stores.address','stores.available','ratings.id as is_rated',DB::raw("(SELECT Count(*) FROM products WHERE store_id = stores.id and active = 1) as number_of_products"),'stores.rate',DB::raw($this->iniDiffLocations('stores', $lat, $lng))])
            ->having('distance','<=',$distance)
            ->orderBy('distance')
            ->first();
        }

        $categories =  Category::Join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
        ->where('categories_translations.locale', $this->lang_code)
        ->where('categories.active', true)
        ->where('categories.parent_id', 0)
        ->orderBy('categories.this_order')
        ->select($columns)
        ->get();

        if ($user) {
            return _api_json(Category::transformCollection($categories),['store' => Store::transform($store)]);
        }else{
            return _api_json(Category::transformCollection($categories));
        } 
    }

}
