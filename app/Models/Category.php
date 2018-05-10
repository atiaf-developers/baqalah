<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends MyModel {

    protected $table = "categories";
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
    );

    public static function getAll() {
        return static::join('categories_translations as trans', 'categories.id', '=', 'trans.category_id')
                        ->select('categories.id', "trans.title")
                        ->orderBy('categories.this_order', 'ASC')
                        ->where('trans.locale', static::getLangCode())
                        ->get();
    }

    public function translations() {
        return $this->hasMany(CategoryTranslation::class, 'category_id');
    }

    public static function transform($item) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $transformer->title = $item->title;
        if ($item->image) {
            $transformer->image = url('public/uploads/categories').'/'.$item->image;
        }
        return $transformer;
    }



    public static function transformFrontHome($item) {

        $item->image = url('public/uploads/categories/m_' . static::rmv_prefix($item->image));
        $item->url = _url('category/' . $item->slug);

        return $item;
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($category) {
            foreach ($category->translations as $translation) {
                $translation->delete();
            }
        });

        static::deleted(function($category) {
            Category::deleteUploaded('categories', $category->image);
        });
    }

}
