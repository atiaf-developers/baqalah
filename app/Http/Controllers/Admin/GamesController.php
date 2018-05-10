<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\BackendController;
use App\Models\Game;
use App\Models\GameTranslation;
use App\Models\Category;
use Validator;
use DB;

class GamesController extends BackendController {

    private $rules = array(
        'price' => 'required',
        'discount_price' => 'required',
        'over_price' => 'required',
        'active' => 'required',
        'category_order' => 'required',
        'offers_order' => 'required',
        'best_order' => 'required',
        'category' => 'required',
        'gallery.0' => 'required|image|mimes:gif,png,jpeg|max:1000',
        'gallery.1' => 'required|image|mimes:gif,png,jpeg|max:1000',
        'gallery.2' => 'required|image|mimes:gif,png,jpeg|max:1000'
    );

    public function __construct() {

        parent::__construct();
        $this->middleware('CheckPermission:games,open', ['only' => ['index']]);
        $this->middleware('CheckPermission:games,add', ['only' => ['store']]);
        $this->middleware('CheckPermission:games,edit', ['only' => ['show', 'update']]);
        $this->middleware('CheckPermission:games,delete', ['only' => ['delete']]);
    }

    public function index(Request $request) {


        return $this->_view('games/index', 'backend');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        $this->data['categories'] = Category::getAll();
        return $this->_view('games/create', 'backend');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //dd($request->file('gallery'));
        $columns_arr = array(
            'title' => 'required|unique:games_translations,title'
        );
        $this->rules = array_merge($this->rules, $this->lang_rules($columns_arr));
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }
        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $gallery = [];

            foreach ($request->file('gallery') as $one) {
                $gallery[] = Game::upload($one, 'games', true);
            }

            $game = new Game;
            $game->youtube_url = $request->input('youtube_url');
            $game->category_id = $request->input('category');
            $game->price = $request->input('price');
            $game->discount_price = $request->input('discount_price');
            $game->over_price = $request->input('over_price');
            $game->active = $request->input('active');
            $game->category_order = $request->input('category_order');
            $game->offers_order = $request->input('offers_order');
            $game->best_order = $request->input('best_order');
            $game->gallery = json_encode($gallery);
            $game->slug = str_slug($title['en']);

            $game->save();

            $game_translations = array();
            $description = $request->input('description');
            foreach ($title as $key => $value) {
                $game_translations[] = array(
                    'locale' => $key,
                    'title' => $value,
                    'description' => $description[$key],
                    'game_id' => $game->id
                );
            }

            GameTranslation::insert($game_translations);

            DB::commit();
            return _json('success', _lang('app.added_successfully'));
        } catch (\Exception $ex) {
            dd($ex);
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        $find = game::find($id);
        if ($find) {
            return _json('success', $find);
        } else {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        $game = Game::find($id);

        if (!$game) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }

        $this->data['translations'] = GameTranslation::where('game_id', $id)->get()->keyBy('locale');
        $game->gallery = json_decode($game->gallery);
        $this->data['game'] = $game;
        $this->data['categories'] = Category::getAll();

        return $this->_view('games/edit', 'backend');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {


        $game = Game::find($id);
        if (!$game) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        $this->rules['gallery.0'] = 'image|mimes:gif,png,jpeg|max:1000';
        $this->rules['gallery.1'] = 'image|mimes:gif,png,jpeg|max:1000';
        $this->rules['gallery.2'] = 'image|mimes:gif,png,jpeg|max:1000';
        $columns_arr = array(
            'title' => 'required|unique:games_translations,title,' . $id . ',game_id'
        );

        $this->rules = array_merge($this->rules, $this->lang_rules($columns_arr));
        $validator = Validator::make($request->all(), $this->rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            return _json('error', $errors);
        }


        DB::beginTransaction();
        try {
            $title = $request->input('title');
            $gallery = json_decode($game->gallery);
            if ($request->file('gallery')) {
                foreach ($request->file('gallery') as $key => $one) {
                    $gallery[$key] = Game::upload($one, 'games', true);
                }
            }

            $game->youtube_url = $request->input('youtube_url');
            $game->category_id = $request->input('category');
            $game->price = $request->input('price');
            $game->discount_price = $request->input('discount_price');
            $game->over_price = $request->input('over_price');
            $game->active = $request->input('active');
            $game->category_order = $request->input('category_order');
            $game->offers_order = $request->input('offers_order');
            $game->best_order = $request->input('best_order');
            $game->gallery = json_encode($gallery);
            $game->slug = str_slug($title['en']);
            $game->save();

            $game_translations = array();
            $description = $request->input('description');
            foreach ($title as $key => $value) {
                $game_translations[] = array(
                    'locale' => $key,
                    'title' => $value,
                    'description' => $description[$key],
                    'game_id' => $game->id
                );
                GameTranslation::updateOrCreate(
                        ['locale' => $key, 'game_id' => $game->id], ['title' => $value, 'description' => $description[$key]]);
            }

            DB::commit();
            return _json('success', _lang('app.updated_successfully'));
        } catch (\Exception $ex) {
            //dd($ex->getMessage());
            DB::rollback();
            return _json('error', _lang('app.error_is_occured'), 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $game = Game::find($id);
        if (!$game) {
            return _json('error', _lang('app.error_is_occured'), 404);
        }
        DB::beginTransaction();
        try {
            foreach (json_decode($game->gallery) as $one) {
                Game::deleteUploaded('games', $one);
            }
            $game->delete();
            DB::commit();
            return _json('success', _lang('app.deleted_successfully'));
        } catch (\Exception $ex) {
            DB::rollback();
            //dd($ex->getMessage());
            if ($ex->getCode() == 23000) {
                return _json('error', _lang('app.this_record_can_not_be_deleted_for_linking_to_other_records'), 400);
            } else {
                return _json('error', _lang('app.error_is_occured'), 400);
            }
        }
    }

    public function data(Request $request) {
        $games = Game::join('games_translations', 'games.id', '=', 'games_translations.game_id')
                ->join('categories', 'categories.id', '=', 'games.category_id')
                ->join('categories_translations', 'categories.id', '=', 'categories_translations.category_id')
                ->where('games_translations.locale', $this->lang_code)
                ->where('categories_translations.locale', $this->lang_code)
                ->select([
            'games.id', "games.gallery", "games.created_at", "games.price", "games.discount_price",
            "games_translations.title", "games.category_order", "games.offers_order", "games.best_order", "games.active",
            "categories_translations.title as category"
        ]);

        return \Datatables::eloquent($games)
                        ->addColumn('options', function ($item) {

                            $back = "";
                            if (\Permissions::check('games', 'edit') || \Permissions::check('games', 'delete')) {
                                $back .= '<div class="btn-group">';
                                $back .= ' <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> options';
                                $back .= '<i class="fa fa-angle-down"></i>';
                                $back .= '</button>';
                                $back .= '<ul class = "dropdown-menu" role = "menu">';
                                if (\Permissions::check('games', 'edit')) {
                                    $back .= '<li>';
                                    $back .= '<a href="' . route('games.edit', $item->id) . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.edit');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                if (\Permissions::check('games', 'delete')) {
                                    $back .= '<li>';
                                    $back .= '<a href="" data-toggle="confirmation" onclick = "Games.delete(this);return false;" data-id = "' . $item->id . '">';
                                    $back .= '<i class = "icon-docs"></i>' . _lang('app.delete');
                                    $back .= '</a>';
                                    $back .= '</li>';
                                }

                                $back .= '</ul>';
                                $back .= ' </div>';
                            }
                            return $back;
                        })
                        ->addColumn('active', function ($item) {
                            if ($item->active == 1) {
                                $message = _lang('app.active');
                                $class = 'label-success';
                            } else {
                                $message = _lang('app.not_active');
                                $class = 'label-danger';
                            }
                            $back = '<span class="label label-sm ' . $class . '">' . $message . '</span>';
                            return $back;
                        })
                        ->addColumn('image', function ($item) {
                            $gallery = json_decode($item->gallery);
                            $back = '<img src="' . url('public/uploads/games/' . $gallery[0]) . '" style="height:64px;width:64px;"/>';

                            return $back;
                        })
                        ->escapeColumns([])
                        ->make(true);
    }

}
