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

    	return $transformer;
    }

}
