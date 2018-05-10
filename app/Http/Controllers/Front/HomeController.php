<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\FrontController;

use App\Models\News;
use App\Models\Album;
use App\Models\Activity;
use App\Models\Video;
use App\Models\ContactMessage;

class HomeController extends FrontController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $this->data['news'] = $this->getNews();
        $this->data['activities'] = $this->getActivities();
        $this->data['albums'] = $this->getAlbums();
        $this->data['video'] = $this->getVideo();
        $this->data['types'] = ContactMessage::$types;
       
        return $this->_view('index');
    }


    private function getNews()
    {
    	$news = News::Join('news_translations','news.id','=','news_translations.news_id')
                          ->where('news_translations.locale',$this->lang_code)
                          ->where('news.active',true)
                          ->orderBy('news.this_order')
                          ->select('news.id','news.images','news.created_at','news_translations.title','news_translations.description','news.slug')
                          ->limit(5)
                          ->get();
                         
          return News::transformCollection($news,'Home');
    }

    private function getActivities()
    {
    	 $activities = Activity::Join('activities_translations','activities.id','=','activities_translations.activity_id')
                                   ->where('activities_translations.locale',$this->lang_code)
                                   ->where('activities.active',true)
                                   ->orderBy('activities.this_order')
                                   ->select( "activities.images","activities_translations.title","activities_translations.description",'activities.slug')
                                   ->limit(4)
                                   ->get();

            return Activity::transformCollection($activities,'Home');
    }

    private function getAlbums()
    {
    	 $albums = Album::Join('albums_translations','albums.id','=','albums_translations.album_id')
                                   ->where('albums_translations.locale',$this->lang_code)
                                   ->where('albums.active',true)
                                   ->orderBy('albums.this_order')
                                   ->select("albums.id","albums_translations.title",'albums.slug')
                                   ->limit(6)
                                   ->get();

        return Album::transformCollection($albums,'Home');
    }

    private function getVideo()
    {
    	$video = Video::Join('videos_translations','videos.id','=','videos_translations.video_id')
                                   ->where('videos_translations.locale',$this->lang_code)
                                   ->where('videos.active',true)
                                   ->orderBy('videos.this_order')
                                   ->select("videos.id","videos_translations.title",'videos.url')
                                   ->first();

        return $video;
    }



}
