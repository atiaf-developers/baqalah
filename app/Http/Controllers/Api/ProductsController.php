<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Validator;
use App\Models\Product;
use App\Models\Favourite;

class ProductsController extends ApiController {

    private $rules = array(
        'category' => 'required',
        'store_id' => 'required',
        'description' => 'required',
        'price' => 'required',
        'quantity' => 'required',
        'has_offer' => 'required',
        'images' => 'required',
    );

    private $favourite_rules = array(
        'product_id' => 'required'
    );

    public function __construct() {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        try {
            $products = $this->getProducts($request);
            return _api_json($products);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json([], ['message' => $message], 400);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id) {
        try {
            $user = $this->auth_user();
            $product = $this->getProducts($request,$id);
            if (!$product) {
                $message = _lang('app.not_found');
                return _api_json(new \stdClass(), ['message' => $message], 404);
            }
            return _api_json($product);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json(new \stdClass(), ['message' => $message], 400);
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        try {

            $this->rules['product_name'] =  "required|unique:products,name,NULL,id,store_id,{$request->input('store_id')}";
            
            $validator = Validator::make($request->all(), $this->rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            }

            $product = new Product;
            $product->name = $request->input('product_name');
            $product->category_id = $request->input('category');
            $product->store_id = $request->input('store_id');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->quantity = $request->input('quantity');
            $product->has_offer = $request->input('has_offer');
            $images = [];
            foreach (json_decode($request->input('images')) as $image) {
                $image = preg_replace("/\r|\n/", "", $image);
                if (!isBase64image($image)) {
                    continue;
                }
                $images[] = Product::upload($image, 'products', true, false, true);
            }
            $product->images = json_encode($images);
            $product->save();
            return _api_json('', ['message' => _lang('app.added_successfully')]);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        try {

            $product = Product::where('id',$id)
            ->where('store_id',$request->input('store_id'))
            ->where('active',true)
            ->first();
            if (!$product) {
                $message = _lang('app.not_found');
                return _api_json('', ['message' => $message], 404);
            }
            $this->rules['product_name'] = "required|unique:products,name,{$id},id,store_id,{$request->input('store_idg')}";

            $validator = Validator::make($request->all(), $this->rules);
            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                return _api_json('', ['errors' => $errors], 400);
            }

            $product->name = $request->input('product_name');
            $product->category_id = $request->input('category');
            $product->store_id = $request->input('store_id');
            $product->description = $request->input('description');
            $product->price = $request->input('price');
            $product->quantity = $request->input('quantity');
            $product->has_offer = $request->input('has_offer');

            $old_images = json_decode($product->images);

            $images = [];
            foreach (json_decode($request->input('images')) as $image) {
                $image = preg_replace("/\r|\n/", "", $image);
                if (!isBase64image($image)) {
                    continue;
                }
                $images[] = Product::upload($image, 'products', true, false, true);
            }
            $product->images = json_encode($images);
            $product->save();

            foreach ($old_images as $image) {
                Product::deleteUploaded('products', $image);
            }
            return _api_json('', ['message' => _lang('app.updated_successfully')]);
        } catch (\Exception $e) {
            $message = _lang('app.error_is_occured');
            return _api_json('', ['message' => $message], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id) {
        try {
            $product = Product::where('id',$id)
            ->where('store_id',$request->input('store_id'))
            ->first();
            if (!$product) {
                $message = _lang('app.not_found');
                return _api_json('', ['message' => $message], 404);
            }
            $product->delete();
            return _api_json('', ['message' => _lang('app.deleted_successfully')]);
        } catch (\Exception $e) {
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

    private function getProducts($request,$product_id = null)
    {

        $columns=["products.id",'products.name','products.description','products.images','products.quantity',
        'products.price'];

        $user = $this->auth_user();

        $products = Product::join('stores', 'stores.id', '=', 'products.store_id');
        $products->join('categories','categories.id','=','products.category_id');
        $products->join('categories_translations', 'categories.id', '=', 'categories_translations.category_id');

        if ($user->type == 1) {

            $products->leftJoin('favourites', function ($join) use($user) {
                $join->on('favourites.product_id', '=', 'products.id');
                $join->where('favourites.user_id', $user->id);    
            });
            $columns[]="favourites.id as is_favourite";
            $columns[]="stores.id as store_id";
            $columns[]="stores.name as store_name";
            $columns[]="stores.image as store_image";
            $columns[]="stores.rate as store_rate";
            $columns[]="stores.available as store_available";

            if ($request->input('categories')) {
                $categories = json_decode($request->input('categories'));
                $products->whereIn('products.category_id',$categories);
            }
            if ($request->input('sort')) {
                $sort_type = $request->input('sort') == 1 ? 'ASC' : 'DESC'; 
                $products->orderBy('products.price', $sort_type);
            }
        }
        else if($user->type == 2){
            $columns[]="products.has_offer";
            $columns[]="categories_translations.title as category";
            $columns[]="categories.id as category_id";
        }

        if ($product_id) {
            $products->where('products.id', $product_id);
        }

        $products->where('stores.id', $request->input('store_id'));
        $products->where('stores.active', true);
        $products->where('products.active', true);
        $products->where('categories_translations.locale', $this->lang_code);
        $products->select($columns);

        if ($product_id) {
            $product = $products->first();
            if (!$product) {
                return false;
            }
            return Product::transform($product);
        }else{
           $products = $products->paginate($this->limit);
           return Product::transformCollection($products);
       }
   }

}
