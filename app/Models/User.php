<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;



class User extends Authenticatable {

    use Notifiable;
    use ModelTrait;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $casts = array(
        'id' => 'integer',
        'mobile' => 'string',
        'gender' => 'integer'
    );
    public static $sizes = array(
        's' => array('width' => 120, 'height' => 120),
        'm' => array('width' => 400, 'height' => 400),
    );

    private static $gender = [
        1 => 'male',
        2 => 'female'
    ];
    
    

    public function store()
    {
        return $this->hasOne(Store::class,'user_id');
    }

    public static function transform($item)
    {
        $transformer = new \stdClass();
        if ($item->type == 1) {
            $transformer->first_name = $item->fname;
            $transformer->last_name = $item->lname;
            $transformer->gender = $item->gender;
            $transformer->gender_text = _lang('app.'.static::$gender[$item->gender]);
            $transformer->mobile = $item->mobile;
        }
        $transformer->username = $item->username;
        $transformer->email = $item->email ? $item->email : "";
        $image = "";
        if (!$item->image) {
             if ($item->gender == 1) {
                $image = 'default_male.png';
            }else if($item->gender == 2){
                $image = 'default_female.png';
            } 
        }else{
            $image = $item->image;
        }
        $transformer->image = url('public/uploads/users').'/'.$image;
        
        if ($item->type == 2) {
            $transformer->store = Store::transform($item->store,['user' => $item]);
        }
        return $transformer;
    }
    
    protected static function boot() {
        parent::boot();

        static::deleted(function($user) {
            if ($user->image != 'default_male.png' || $user->image != 'default_female.png') {
                User::deleteUploaded('users',$user->image);
            }
            
        });
    }
   

}
