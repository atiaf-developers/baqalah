<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends MyModel {

    protected $table = 'contact_messages';

    public static $types=['suggestion','enquiry','complaint'];

    public static function transform($item)
    {
    	$transformer = new \stdClass();
    	$transformer->name = $item->name;
    	$transformer->email = $item->email;
    	$transformer->subject = $item->subject;
        $transformer->message = $item->message;
    	$transformer->gender = $item->gender;
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

    	return $transformer;
    }

}
