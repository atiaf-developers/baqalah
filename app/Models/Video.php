<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends MyModel
{
    protected $table = "videos";

	public function translations() {
		return $this->hasMany(VideoTranslation::class, 'video_id');
	}

	public static function transform($item)
	{
		$transformer = new \stdClass();
		
		$transformer->title = $item->title;
		$transformer->url = "https://www.youtube.com/embed"."/".$item->youtube_url;

       return $transformer;
		
	}



	protected static function boot() {
		parent::boot();

		static::deleting(function($video) {
			foreach ($video->translations as $translation) {
				$translation->delete();
			}
		});

	
	}


}
