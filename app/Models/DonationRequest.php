<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTrait;

class DonationRequest extends Model {

    use ModelTrait;

    protected $table = "donation_requests";
    public static $status_filter = [
        0 => 'pending',
        1 => 'assigned',
        2 => 'start',
        3 => 'i_have_arrived',
        4 => 'Received',
    ];
    public static $status_text = [
        0 => [
            'client' => ['message_ar' => '', 'message_en' => 'pending'],
            'delegate' => ['message_ar' => '', 'message_en' => 'pending'],
            'admin' => ['message_ar' => '', 'message_en' => 'pending']
        ],
        1 => [
            'client' => ['message_ar' => 'جارى', 'message_en' => 'assigned'],
            'delegate' => ['message_ar' => 'بدأ', 'message_en' => 'start'],
            'admin' => ['message_ar' => 'جارى', 'message_en' => 'assigned']
        ],
        2 => [
            'client' => ['message_ar' => 'المندوب قادم اليك لاستلام التبرع', 'message_en' => 'The delegate is coming to you to receive the donation'],
            'delegate' => ['message_ar' => 'جارى', 'message_en' => 'i have arrived'],
            'admin' => ['message_ar' => 'جارى', 'message_en' => 'pending']
        ],
        3 => [
            'client' => ['message_ar' => 'لقد وصل المندوب لاستلام التبرع', 'message_en' => 'The delegate has arrived to receive the donation'],
            'delegate' => ['message_ar' => 'جارى', 'message_en' => 'Received'],
            'admin' => ['message_ar' => 'جارى', 'message_en' => 'pending']
        ],
        4 => [
            'client' => ['message_ar' => 'لقد تم استلام التبرع عن طريق المندوب', 'message_en' => 'The donation was received by the delegate'],
            'delegate' => ['message_ar' => 'جارى', 'message_en' => 'donation has been received successfully'],
            'admin' => ['message_ar' => 'جارى', 'message_en' => 'pending']
        ],
    ];

    public static function transform($item) {
        $transformer = new \stdClass();
        $transformer->id = $item->id;
        $lang_code=static::getLangCode();
        if ($lang_code == 'ar') {
            $transformer->date = ArabicDateSpecial($item->appropriate_time);
        } else {
            $transformer->date = date('l ,F j , Y h:i A', strtotime($item->appropriate_time));
        }
        $transformer->donation_type = $item->donation_type;
        $transformer->description = $item->description;
        $prefixed_array = preg_filter('/^/', url('public/uploads/donation_requests') . '/', json_decode($item->images));
        $transformer->images = $prefixed_array;
        $transformer->name = $item->name;
        if ($item->mobile) {
            $transformer->mobile = $item->mobile;
        }
        if ($item->lat && $item->lng) {
            $transformer->lat = $item->lat;
            $transformer->lng = $item->lng;
        }
        $transformer->status = $item->status;
        $transformer->status_text = static::$status_text[$item->status]['delegate']['message_'.$lang_code];
        return $transformer;
    }

}
