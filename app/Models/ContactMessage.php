<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends MyModel {

    protected $table = 'contact_messages';

    public static $types=['suggestion','enquiry','complaint'];

}
