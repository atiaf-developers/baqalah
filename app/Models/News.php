<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends MyModel {

    protected $table = "news";

    public static $sizes = array(
        's' => array('width' => 200, 'height' => 200),
        'm' => array('width' => 400, 'height' => 400),
    );



    public function translations() {
        return $this->hasMany(NewsTranslation::class, 'news_id');
    }

    public static function transform($item) {
        $lang = static::getLangCode();
        $transformer = new \stdClass();
        $transformer->title = $item->title;
        $transformer->description = $item->description;
        $prefixed_array = preg_filter('/^/', url('public/uploads/news') . '/', json_decode($item->images));
        $transformer->images = $prefixed_array;
        $transformer->url = url("$lang/news-and-events/$item->slug");
        $transformer->created_at = date('d/m/Y', strtotime($item->created_at));

        return $transformer;
    }

    public static function transformHome($item) {
        $transformer = new \stdClass();
        $transformer->slug = $item->slug;
        $transformer->title = $item->title;
        $transformer->description = mb_strimwidth($item->description, 0, 100, '...');
        $news_images =  json_decode($item->images);
        $news_image_without_prefix =  static::rmv_prefix($news_images[0]);
        $transformer->image = url('public/uploads/news') . '/m_' .$news_image_without_prefix;
        $transformer->created_at = date('d/m/Y', strtotime($item->created_at));
        $transformer->url = _url('news/'.$transformer->slug);

        return $transformer;
    }

    public static function transformDetailes($item) {

        $transformer = new \stdClass();
        $transformer->slug = $item->slug;
        $transformer->title = $item->title;
        $transformer->description =$item->description;
        $news_images =  json_decode($item->images);
        foreach ($news_images as $key => $value) {
            $news_images[$key] =  static::rmv_prefix($value);
        }
        $prefixed_array = preg_filter('/^/', url('public/uploads/news') . '/m_', $news_images);
        $transformer->images = $prefixed_array;
        $transformer->created_at = date('d/m/Y', strtotime($item->created_at));

        return $transformer;
    }

    

    protected static function boot() {
        parent::boot();

        static::deleting(function($news) {
            foreach ($news->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($news) {
            $old_images = json_decode($news->images);

            foreach ($old_images as $key => $value) {
                static::deleteUploaded('news', $value);
            }
        });
    }

}
