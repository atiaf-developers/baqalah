<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends MyModel {

    protected $table = "games";
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
    );

    public static function getAll() {
        return static::join('games_translations as trans', 'games.id', '=', 'trans.game_id')
                        ->select('games.id', "trans.title")
                        ->orderBy('games.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->get();
    }

    public function translations() {
        return $this->hasMany(GameTranslation::class, 'game_id');
    }

    public static function transform($item) {
        $gallery = json_decode($item->gallery);
        //dd($gallery);
        $item->image = url('public/uploads/games/m_' . static::rmv_prefix($gallery[0]));
        $item->url = _url('game/' . $item->slug);

        return $item;
    }
    public static function transformFrontHome($item) {
        $gallery = json_decode($item->gallery);
        //dd($gallery);
        $item->image = url('public/uploads/games/m_' . static::rmv_prefix($gallery[0]));
        $item->url = _url('game/' . $item->slug);

        return $item;
    }
    public static function transformGameDetails($item) {
        $gallery = json_decode($item->gallery);
        foreach($gallery as $key=>$value){
            $gallery[$key]=url('public/uploads/games/m_' . static::rmv_prefix($gallery[$key]));
        }
        $item->gallery=$gallery;
        return $item;
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($game) {
            foreach ($game->translations as $translation) {
                $translation->delete();
            }
        });
    }

}
