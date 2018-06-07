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
        'name' => 'required'
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
                $user = $this->auth_user();
                $ContactMessage = new ContactMessage;
                $ContactMessage->name = $request->input('name');
                $ContactMessage->email = $request->input('email');
                $ContactMessage->subject = $request->input('subject');
                $ContactMessage->message = $request->input('message');
                $ContactMessage->user_id = $user->id;
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
            $complaints = ContactMessage::Join('users','users.id','=','contact_messages.user_id')
            ->where('store_id',$request->input('store_id'))
            ->select('contact_messages.name','contact_messages.email','contact_messages.subject','contact_messages.message','users.gender')
            ->paginate($this->limit);
            return _api_json(ContactMessage::transformCollection($complaints));
        } catch (\Exception $ex) {
            return _api_json([], ['message' => _lang('app.error_is_occured')], 400);
        }
        
    }


    public function getCategories(Request $request) {
        try {
            $user = $this->auth_user();
            if (!$user || $user->type == 1) {
                $validator = Validator::make($request->all(), $this->categories_rules);
                if ($validator->fails()) {
                    $errors = $validator->errors()->toArray();
                    return _api_json([], ['errors' => $errors], 400);
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
                return _api_json([], ['errors' => $errors], 400);
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
        $category_columns = ["categories.id", "categories_translations.title"];

        $store_columns = ['stores.id','stores.name','stores.description','stores.image','stores.mobile','stores.lat','stores.lng','stores.address','stores.available',DB::raw("(SELECT Count(*) FROM products WHERE store_id = stores.id and active = 1 and deleted_at IS NULL) as number_of_products"),'stores.rate'];
        
        if (($user && $user->type == 1) || !$user) {
          $category_columns[] = "categories.image";
          $store =  $this->getStores($request);
          if (!empty($store)) {
              $store = $store[0];
          }else{
            $store = new \stdClass();
          }
        }
       

        $categories =  Category::Join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
        ->where('categories_translations.locale', $this->lang_code)
        ->where('categories.active', true)
        ->where('categories.parent_id', 0)
        ->orderBy('categories.this_order')
        ->select($category_columns)
        ->get();

        if (($user && $user->type == 1) || !$user) {
            return _api_json(Category::transformCollection($categories),['store' => $store]);
        }else{
            return _api_json(Category::transformCollection($categories));
        } 
    }

}
