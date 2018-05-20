<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Helpers\AUTHORIZATION;
use App\Models\User;
use App\Models\Product;
use App\Models\Store;
use App\Models\StoreCtegory;
use App\Models\Rating;
use App\Models\Favourite;
use Validator;
use DB;

class UserController extends ApiController {
   
    private $rate_rules = array(
        'store_id' => 'required',
        'rate' => 'required'
    );

    private $favourite_rules = array(
        'product_id' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

    protected function update(Request $request) {
            $user = $this->auth_user();
            if ($user->type == 2) {
                $store = Store::where('user_id',$user->id)->first();
            }
            $rules = array();
            
            if ($request->input('username')) {
                $rules['username'] = "required|unique:users,username,$user->id";
            }
            if ($request->input('email')) {
                $rules['email'] = "required|email|unique:users,email,$user->id";
            }
            if ($request->input('image')) {
                $rules['image'] = "required";
            }
            if ($request->input('mobile')) {
                $rules['mobile'] =  $user->type == 1 ? "required|unique:users,mobile,$user->id":"required|unique:stores,mobile,$store->id";
            }
            if ($request->input('password')) {
                $rules['password'] = "required";
            }

            if ($user->type == 1) {

                if ($request->input('first_name')) {
                   $rules['first_name'] = "required";
                }
                if ($request->input('last_name')) {
                    $rules['last_name'] = "required";
                }
                if ($request->input('gender')) {
                    $rules['gender'] = "required";
                }
                
            }else if($user->tpye == 2){
                if ($request->input('store_name')) {
                    $rules['store_name'] = "required";
                }
                if ($request->input('store_description')) {
                    $rules['store_description'] = "required";
                }
                if ($request->input('store_image')) {
                    $rules['store_image'] = "required";
                }
                if ($request->input('lat')) {
                    $rules['lat'] = "required";
                }
                if ($request->input('lng')) {
                    $rules['lng'] = "required";
                }
                if ($request->input('store_categories')) {
                    $rules['store_categories'] = "required";
                } 
            }

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json(new \stdClass(), ['errors' => $errors], 400);
            } else {

                DB::beginTransaction();
                try {
                    if ($request->input('username')) {
                        $user->username = $request->input('username');
                    }
                    if ($request->input('email')) {
                        $user->email = $request->input('email');
                    }
                    if ($request->input('mobile')) {
                        if ($user->type == 1) {
                            $user->mobile = $request->input('mobile');
                        }else{
                            $store->mobile = $request->input('mobile');
                        }
                    }
                    if ($password = $request->input('password')) {
                        $user->password = bcrypt($request->input('password')); 
                    }
                    if ($image=$request->input('image')) {
                        $image = preg_replace("/\r|\n/", "", $image);
                        if ($user->image != 'default.png') {
                            User::deleteUploaded('users', $user->image);
                        }
                        if (isBase64image($image)) {
                            $user->image = User::upload($image, 'users', true, false, true);
                        }
                    }
                    if ($user->type == 1) {
                        if ($request->input('first_name')) {
                           $user->fname = $request->input('first_name');
                        }
                        if ($request->input('last_name')) {
                            $user->lname = $request->input('last_name');
                        }
                        if ($request->input('gender')) {
                            $user->gender = $request->input('gender');
                        }
                    }else if($user->type == 2){
                        if ($request->input('store_name')) {
                           $store->name = $request->input('store_name');
                        }
                        if ($request->input('store_description')) {
                            $store->description = $request->input('store_description');
                        }
                        if ($request->input('store_image')) {
                            $image = preg_replace("/\r|\n/", "", $request->input('store_image'));
                            Store::deleteUploaded('stores', $store->image);
                            if (isBase64image($image)) {
                                $store->image = Store::upload($image,'stores',true,false,true);
                            } 
                        }
                        if ($request->input('lat')) {
                            $store->lat = $request->input('lat');
                        }
                        if ($request->input('lng')) {
                            $store->lng = $request->input('lng');
                        }

                        if ($request->input('available') != null) {
                            $store->available = $request->input('available');
                        }
                        $store->address = getAddress($request->input('lat'), $request->input('lng'), $lang = "AR"); 
                       
                        if ($request->input('store_categories')) {

                            $store_categories = $this->storeCategories($store->id)->pluck('id')->toArray();
                            $new_categories = json_decode($request->input('store_categories'));
                            $diff = array_diff($store_categories, $new_categories);
                            
                            if (count($diff) > 0) {
                                Product::where('store_id',$store->id)->whereIn('category_id',$diff)->delete();
                            }
                            StoreCtegory::where('store_id', $store->id)->delete();
                            $store_categories = array();
                            
                            foreach ($new_categories as $value) {
                                $store_categories[] = array(
                                    'store_id' => $store->id,
                                    'category_id' => $value
                                );
                            }
                            StoreCtegory::insert($store_categories);
                        }
                       
                        $store->save();
                    }
                    $user->save();
                    $user = User::transform($user);
                    DB::commit();
                    return _api_json($user, ['message' => _lang('app.updated_successfully')]);
                } catch (\Exception $e) {
                    dd($e);
                    $message = _lang('app.error_is_occured');
                    return _api_json(new \stdClass(), ['message' => $message], 400);
                }
            }
    }

    public function getUser()
    {
        try {
            $user = User::transform($this->auth_user());
            return _api_json($user);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json(new \stdClass(), ['message' => $message], 400);
        }
    }

    public function rate(Request $request) {

        $validator = Validator::make($request->all(), $this->rate_rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _api_json('', ['errors' => $errors], 400);
        }
        
        $user = $this->auth_user();
        $store = Store::find($request->input('store_id'));
        if (!$store) {
            $message = _lang('app.not_found');
            return _api_json('', ['message' => $message], 404);
        }
        DB::beginTransaction();
        try {
            
            $rate = new Rating;
            $rate->user_id = $user->id;
            $rate->store_id = $request->input('store_id');
            $rate->rate = $request->input('rate');
            $rate->save();

            $store_new_rate = Rating::where('store_id', $request->input('store_id'))
            ->select(DB::raw(' SUM(rate)/COUNT(*) as rate'))
            ->first();
            $store->rate = $store_new_rate->rate;
            $store->save();
            DB::commit();
            $message = _lang('app.rated_successfully');
            return _api_json('', ['message' => $message]);
        } catch (\Exception $e) {
            DB::rollback();
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

    public function handleFavourites(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), $this->favourite_rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            } 

            $user = $this->auth_user();
            $product = Product::find($request->input('product_id'));
            if (!$product) {
                $message = _lang('app.not_found');
                return _api_json('', ['message' => $message], 404);
            }

            $check = Favourite::where('product_id',$request->input('product_id'))
            ->where('user_id',$user->id)
            ->first();

            if ($check) {
                $check->delete();
            }
            else{
                $favourite = new Favourite;
                $favourite->product_id = $request->input('product_id');
                $favourite->user_id = $user->id;
                $favourite->save();
            }
            return _api_json('',['message' => _lang('app.updated_successfully')]);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message],400);
        }
    }


    public function favourites(Request $request) {
        try {
            $user = $this->auth_user();

            $favourites = Product::Join('favourites', function ($join) use($user) {
                $join->on('favourites.product_id', '=', 'products.id');
                $join->where('favourites.user_id', $user->id);    
            }) 
            ->join('stores', 'stores.id', '=', 'products.store_id')
            ->where('stores.active',true)
            ->select("products.id",'products.name','products.description','products.images','products.quantity',
                        'products.price',"favourites.id as is_favourite","stores.id as store_id","stores.name as store_name","stores.image as store_image","stores.rate as store_rate","stores.available as store_available")
            ->paginate($this->limit);

            return _api_json(Product::transformCollection($favourites));
        } catch (\Exception $e) {
            $message = ['message' => _lang('app.error_occured')];
            return _api_json([], $message, 400);
        }
    }

    public function logout(Request $request) {
        Device::where('user_id', $this->auth_user()->id)->where('device_id', $request->input('device_id'))->update(['device_token'=>'']);
        return _api_json(new \stdClass(), array(), 201);
    }

}
