<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;


class AlbumImage extends Model
{
	use ModelTrait;
    protected $table = "album_images";
    public static $sizes = array(
        's' => array('width' => 200, 'height' => 200),
        'm' => array('width' => 400, 'height' => 400),
    );


    protected static function boot() {
        parent::boot();

        static::deleted(function($album_image) {
           AlbumImage::deleteUploaded('albums',$album_image->image);
        });
    }


   
}
