<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Input;
class AppServiceProvider extends ServiceProvider {

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Validator::extend('is_png', function($attribute, $value, $params, $validator) {
            $image = base64_decode($value);
            $f = finfo_open();
            $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
            return $result == 'image/png';
        });
        Validator::extend('is_base64', function($attribute, $value, $params, $validator) {
            if (base64_encode(base64_decode($value, true)) === $value) {
                return true;
            } else {
                return false;
            }
        });
        Validator::extend('onefromjsonarray', function($attribute, $value, $params, $validator) {
            $categories = json_decode($value);
            if (!$categories || empty($categories)) {
                return false;
            } else {
                return true;
            }
        });
        Validator::extend('is_base64image', function($attribute, $value, $params, $validator) {
            if (base64_encode(base64_decode($value, true)) === $value) {
                $img = imagecreatefromstring(base64_decode($value));
                if (!$img) {
                    return false;
                }

                imagepng($img, 'tmp.png');
                $info = getimagesize('tmp.png');

                unlink('tmp.png');
                if ($info[0] > 0 && $info[1] > 0 && $info['mime']) {
                    return true;
                }
            }
            return false;
        });
        Validator::extend('upload_count', function($attribute, $value, $parameters) {
            //$files = Input::file($value);
            dd($attribute);
            return (count($files) <= $parameters[1]) ? true : false;
        });

        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }

}
