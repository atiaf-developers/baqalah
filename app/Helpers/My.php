<?php

use Illuminate\Contracts\Routing\UrlGenerator;

if (!function_exists('_url')) {

    function _url($path = null, $parameters = [], $secure = null) {
        $path = app()->getLocale() . '/' . $path;
        if (is_null($path)) {
            return app(UrlGenerator::class);
        }

        return app(UrlGenerator::class)->to($path, $parameters, $secure);
    }

}
if (!function_exists('handleKeywordWhere')) {

    function handleKeywordWhere($columns, $keyword) {
        $search_exploded = explode(" ", $keyword);
        $i = 0;
        $construct = " ";
        foreach ($columns as $col) {
            //pri($col);
            $x = 0;
            $i++;
            if ($i != 1) {
                $construct .= " OR ";
            }
            foreach ($search_exploded as $search_each) {
                $x++;
                if (count($search_exploded) > 1) {
                    if ($x == 1) {
                        $construct .= "($col LIKE '%$search_each%' ";
                    } else {
                        $construct .= "AND $col LIKE '%$search_each%' ";
                        if ($x == count($search_exploded)) {
                            $construct .= ")";
                        }
                    }
                } else {
                    $construct .= "$col LIKE '%$search_each%' ";
                }
            }
        }
        return $construct;
    }

}




if (!function_exists('customer_url')) {

    function customer_url($path = null, $parameters = [], $secure = null) {
        $path = app()->getLocale() . '/customer/' . $path;
        if (is_null($path)) {
            return app(UrlGenerator::class);
        }

        return app(UrlGenerator::class)->to($path, $parameters, $secure);
    }

}
if (!function_exists('_lang')) {

    function _lang($item) {
        if (Lang::has($item)) {
            $line = Lang::get($item);
        } else {
            $item_arr = explode('.', $item);
            array_shift($item_arr);
            $line = end($item_arr);
            $line = str_replace('_', ' ', ucwords($line));
        }

        return $line;
    }

}

if (!function_exists('_json')) {

    function _json($type = 'success', $data = NULL, $http_code = 200) {
        $json = array();
        $json['type'] = $type;
        if ($type == 'error' && is_array($data)) {

            $json['errors'] = $data;
        } else if ($type == 'success' && is_array($data))
            $json['data'] = $data;
        else {
            $json['message'] = $data;
        }


        return response()->json($json, $http_code);
    }

}
if (!function_exists('_api_json')) {

    function _api_json($data = NULL, $extra_params = array(), $http_code = 200) {
        $json = array();
        $json['data'] = $data;
        if (!empty($extra_params)) {
            foreach ($extra_params as $key => $param) {
                $json[$key] = $param;
            }
        }
        if (isset($json['errors'])) {
            foreach ($json['errors'] as $key => $error) {
                $json['errors'][$key] = $error[0];
            }
        }
        return response()->json($json, $http_code, []);
    }

}



if (!function_exists('img_decoder')) {

    function img_decoder($encoded_string, $path) {
        $image_name = time() . uniqid(rand()) . '.jpg';
        $new_path = base_path() . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . $image_name;
        $hashdata = array(" ");
        $with = "+";
        $imageDataEncoded = str_replace($hashdata, $with, $encoded_string);
        $decoded_string = base64_decode($imageDataEncoded);
        $photo = imagecreatefromstring($decoded_string);
        if ($photo !== false) {
            //$rotate = imagerotate($photo, 90, 0);
            imagejpeg($photo, $new_path, 100);
            imagedestroy($photo);
            return $image_name;
        }
    }

}


if (!function_exists('ArabicDate')) {

    function arabicDate($date, $get_all_date = false) {
        $months = array("Jan" => "يناير", "Feb" => "فبراير", "Mar" => "مارس", "Apr" => "أبريل", "May" => "مايو", "Jun" => "يونيو", "Jul" => "يوليو", "Aug" => "أغسطس", "Sep" => "سبتمبر", "Oct" => "أكتوبر", "Nov" => "نوفمبر", "Dec" => "ديسمبر");
        $your_date = $date; // The Current Date
        $en_month = date("M", strtotime($your_date));
        foreach ($months as $en => $ar) {
            if ($en == $en_month) {
                $ar_month = $ar;
            }
        }

        $find = array("Sat", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri");
        $replace = array("السبت", "الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة");
        $ar_day_format = date('D', strtotime($your_date)); // The Current Day
        $ar_day = str_replace($find, $replace, $ar_day_format);

        $standard = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $eastern_arabic_symbols = array("٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩");
        //$current_date = $ar_day . ' ' . date('d', strtotime($your_date)) . ' - ' . $ar_month . ' - ' . date('Y');
        //$current_date = $ar_day . ' ' . date('d', strtotime($your_date)) . ' ' . $ar_month;
        if ($get_all_date) {
            $current_date = $ar_day . ' ' . date('d', strtotime($your_date)) . ' - ' . $ar_month . ' - ' . date('Y');
        } else {
            $current_date = date('d', strtotime($your_date)) . ' ' . $ar_month;
        }

        $arabic_date = str_replace($standard, $eastern_arabic_symbols, $current_date);

        return $arabic_date;
    }

}


if (!function_exists('ArabicDateSpecial')) {

    function ArabicDateSpecial($date,$time=true) {
        $months = array("Jan" => "يناير", "Feb" => "فبراير", "Mar" => "مارس", "Apr" => "أبريل", "May" => "مايو", "Jun" => "يونيو", "Jul" => "يوليو", "Aug" => "أغسطس", "Sep" => "سبتمبر", "Oct" => "أكتوبر", "Nov" => "نوفمبر", "Dec" => "ديسمبر");
        $your_date = $date; // The Current Date
        $en_month = date("M", strtotime($your_date));
        foreach ($months as $en => $ar) {
            if ($en == $en_month) {
                $ar_month = $ar;
            }
        }

        $find = array("Sat", "Sun", "Mon", "Tue", "Wed", "Thu", "Fri");
        $replace = array("السبت", "الأحد", "الإثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة");
        $ar_day_format = date('D', strtotime($your_date)); // The Current Day
        $ar_day = str_replace($find, $replace, $ar_day_format);

        $standard = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $eastern_arabic_symbols = array("٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩");
        //$current_date = $ar_day . ' ' . date('d', strtotime($your_date)) . ' - ' . $ar_month . ' - ' . date('Y');
        //$current_date = $ar_day . ' ' . date('d', strtotime($your_date)) . ' ' . $ar_month;
        if($time)
         $current_date =  date('h:i A',strtotime($your_date)).' '.$ar_day . ',' .$ar_month.' '.date('d', strtotime($your_date)) . ' ,' . date('Y');
        else
        $current_date =  $ar_day . ',' .$ar_month.' '.date('d', strtotime($your_date)) . ' ,' . date('Y');

        //$arabic_date = str_replace($standard, $eastern_arabic_symbols, $current_date);

        return $current_date;
    }

}


if (!function_exists('img_resize')) {

    function img_resize($file_name, $path) {
        $file_path = $path;
        $percent = 0.5;
        // Content type
        header('Content-Type: image/jpeg');

        // Get new sizes
        list($width, $height) = getimagesize($file_path);
        $newwidth = $width * $percent;
        $newheight = $height * $percent;

        // create black image with width and hieght that i want to be in my image
        $ext = end(explode('.', $file_name));
        if (strtolower($ext) == "jpg")
            $source = imagecreatefromjpeg($img);
        elseif (strtolower($ext) == "gif")
            $source = imagecreatefromgif($img);
        elseif (strtolower($ext) == "png")
            $source = imagecreatefrompng($img);
        elseif (strtolower($ext) == "bmp")
            $source = imagecreatefromwbmp($img);
        $destination = imagecreatetruecolor($newwidth, $newheight);


        // Resize
        ImageCopyResampled($destination, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        // Output
        imagejpeg($thumb);
    }

}
if (!function_exists('get_years_days_months_count')) {

    function get_years_days_months_count($date1, $date2) {

        $diff = abs(strtotime($date2) - strtotime($date1));

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        printf("%d years, %d months, %d days\n", $years, $months, $days);
    }

}
if (!function_exists('get_age')) {

    function get_age($any_date, $today_date) {
        $date1 = date_create($any_date);
        $date2 = date_create($today_date);
        $diff = date_diff($date1, $date2);
        $years = $diff->format("%Y");
        return $years;
    }

}
if (!function_exists('startsWith')) {

    function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

}
if (!function_exists('endsWith')) {

    function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

}
if (!function_exists('GetDays')) {

    function GetDays($startDate, $endDate) {

        $startDate = date("Y-m-d", strtotime($startDate));
        $endDate = date("Y-m-d", strtotime($endDate));

        $Days[] = $startDate;

        $currentDate = $startDate;


        while ($currentDate < $endDate) {

            $currentDate = date("Y-m-d", strtotime("+1 day", strtotime($currentDate)));
            //pri($currentDate);
            //$day_name = date("d", $currentDate);


            $Days[] = $currentDate;
            //$Days[][$day_name] = $currentDate;
        }

        return $Days;
    }

}

if (!function_exists('getDaysDatesAndNames')) {

    function getDaysDatesAndNames($from_date, $to_date) {
        $Days = array();
        $from_date = new \DateTime($from_date);
        $to_date = new \DateTime($to_date);
        $Days[][$from_date->format('l')] = $from_date->format('Y-m-d');
        $diff = $to_date->diff($from_date);
        for ($i = 1; $i <= $diff->days; $i++) {
            $from_date->modify('+1 day');
            $Days[][$from_date->format('l')] = $from_date->format('Y-m-d');
        }
        return $Days;
    }

}


if (!function_exists('check_permission')) {

    function check_permission($page, $permission = "open") {
        $user = Auth::guard('admin')->user()->group_id;

//        if (!empty($page)) {
//                if (isset($this->permissions->{$page})) {
//                    if (isset($this->permissions->{$page}->{$permission})) {
//                        if ($this->permissions->{$page}->{$permission} == 1) {
//                            return true;
//                        }
//                    }
//                }
//            }
        return $user;
    }

}
if (!function_exists('resize')) {

    function resize($path, $width, $height, $save_path, $option = 'auto') {
        $params = array('fileName' => $path);
        get_instance()->load->library('resize', $params);
        get_instance()->resize->resizeImage($width, $height, $option);
        get_instance()->resize->saveImage($save_path);
        return $save_path;
    }

}
if (!function_exists('resize2')) {

    function resize2($paths, $names, $dimensions, $option = 'auto') {
        if (!empty($paths)) {
            //pri($paths);
            foreach ($paths as $key1 => $path) {
                $params = array('fileName' => $path);
                get_instance()->load->library('resize', $params);
                foreach ($dimensions as $key2 => $value) {
                    $width = $value['width'];
                    $height = $value['height'];
                    $image_name = $key2 . '_' . $names[$key1];
                    $save_path = 'uploads/programs_slider/' . $image_name;
                    get_instance()->resize->resizeImage($width, $height, $option);
                    get_instance()->resize->saveImage($save_path);
                }
                get_instance()->resize->clear();
            }
        }

        //return $save_path;
    }

}
if (!function_exists('resize4')) {

    function resize4($path, $new_path, $file, $sizes) {

        get_instance()->load->library('image_lib');
        foreach ($sizes as $key => $size) {
            $config['image_library'] = 'gd2';
            $config['source_image'] = $path;
            $config['create_thumb'] = false;
            $config['maintain_ratio'] = true;
            $config['width'] = $size['width'];
            $config['height'] = $size['height'];
            $config['new_image'] = './uploads/places/' . $key . '_' . $file;

            get_instance()->image_lib->clear();
            get_instance()->image_lib->initialize($config);
            get_instance()->image_lib->resize();
        }
        return $file;
        //return $save_path;
    }

}
if (!function_exists('resize5')) {

    function resize5($uploaded_data, $new_path, $sizes = array(), $return_size_in_name = true) {
        $file_uploaded_name = $uploaded_data['file_name'];
        $file_uploaded_name = str_replace(' ', '-', $file_uploaded_name);
        $file_uploaded_path = $uploaded_data['full_path'];
        $file_name = mt_rand(1, 1000000) . '_' . $file_uploaded_name;
        get_instance()->load->library('image_lib');

        foreach ($sizes as $key => $size) {
            $config['image_library'] = 'gd2';
            $config['source_image'] = $file_uploaded_path;
            $config['create_thumb'] = false;
            $config['maintain_ratio'] = false;
            $config['width'] = $size['width'];
            $config['height'] = $size['height'];
            $config['quality'] = 100;
            $config['new_image'] = './' . $new_path . $key . '_' . $file_name;

            get_instance()->image_lib->clear();
            get_instance()->image_lib->initialize($config);
            get_instance()->image_lib->resize();
        }
        //pri($file_name);
        if ($return_size_in_name) {
            return 's_' . $file_name;    //size small
        } else {
            return $file_uploaded_name;    //size small
        }
    }

}
if (!function_exists('resizeOne')) {

    function resizeOne($uploaded_data, $new_path, $sizes = array(), $return_size_in_name = true) {
        $file_uploaded_name = $uploaded_data['file_name'];
        $file_uploaded_name = str_replace(' ', '-', $file_uploaded_name);
        $file_uploaded_path = $uploaded_data['full_path'];
        $file_name = mt_rand(1, 1000000) . '_' . $file_uploaded_name;
        get_instance()->load->library('image_lib');

        foreach ($sizes as $key => $size) {
            $config['image_library'] = 'gd2';
            $config['source_image'] = $file_uploaded_path;
            $config['create_thumb'] = false;
            $config['maintain_ratio'] = true;
            $config['width'] = $size['width'];
            $config['height'] = $size['height'];
            $config['new_image'] = './' . $new_path . $key . '_' . $file_name;

            get_instance()->image_lib->clear();
            get_instance()->image_lib->initialize($config);
            get_instance()->image_lib->resize();
        }

        if ($return_size_in_name) {
            return 's_' . $file_name;    //size small
        } else {
            return $file_name;    //size small
        }
    }

}
if (!function_exists('resize6')) {

    function resize6($uploaded_data, $new_path, $sizes, $size = false) {
        $file_uploaded_name = $uploaded_data['file_name'];
        $file_uploaded_name = str_replace(' ', '-', $file_uploaded_name);
        $file_uploaded_path = $uploaded_data['full_path'];
        $file_name = mt_rand(1, 1000000) . '_' . $file_uploaded_name;
        get_instance()->load->library('image_lib');
        foreach ($sizes as $key => $size) {
            $config['image_library'] = 'gd2';
            $config['source_image'] = $file_uploaded_path;
            $config['create_thumb'] = false;
            $config['maintain_ratio'] = true;
            $config['width'] = $size['width'];
            $config['height'] = $size['height'];
            $config['new_image'] = './' . $new_path . $key . '_' . $file_name;

            get_instance()->image_lib->clear();
            get_instance()->image_lib->initialize($config);
            get_instance()->image_lib->resize();
        }
        return $file_name;
        //return $save_path;
    }

}
if (!function_exists('resize3')) {

    function resize3($path, $file, $sizes) {

        get_instance()->load->library('image_lib');
        foreach ($sizes as $key => $size) {
            $config['image_library'] = 'gd2';
            $config['source_image'] = $path;
            $config['create_thumb'] = false;
            $config['maintain_ratio'] = true;
            $config['width'] = $size['width'];
            $config['height'] = $size['height'];
            $config['new_image'] = './uploads/programs_slider/' . $key . '_' . $file;

            get_instance()->image_lib->clear();
            get_instance()->image_lib->initialize($config);
            get_instance()->image_lib->resize();
        }

        //return $save_path;
    }

}
if (!function_exists('err_404')) {

    function err_404($type = 'front') {
        $CI = & get_instance();
        if ($type == 'front') {
            $CI->load->view('err404');
        } elseif ($type == 'permissions') {
            $CI->load->view('permissions_error');
        }

        echo $CI->output->get_output();
        exit;
    }

}
if (!function_exists('merge')) {

    function merge() {
        $numberOfImages = 3;
        $x = 40;
        $y = 40 * 3;
        $background = imagecreatetruecolor($x, $y * 3);


        $firstUrl = FCPATH . 'img/img_slider01.jpg';

        $secondUrl = FCPATH . 'img/img_slider02.jpg';

        $thirdUrl = FCPATH . 'img/img_slider03.jpg';

        $outputImage = $background;

        $first = imagecreatefromjpeg($firstUrl);
        $second = imagecreatefromjpeg($secondUrl);
        $third = imagecreatefromjpeg($thirdUrl);



        imagecopymerge($outputImage, $first, 0, 0, 0, 0, $x, $y, 100);
        imagecopymerge($outputImage, $second, 0, $y, 0, 0, $x, $y, 100);
        imagecopymerge($outputImage, $third, 0, $y * 2, 0, 0, $x, $y, 100);

        imagejpeg($outputImage, FCPATH . '/uploads/test/test22.jpg');

        imagedestroy($outputImage);
    }

}
if (!function_exists('Random')) {

    function Random($size) {
        $alpha_key = '';
        $keys = range(0, 9);

        for ($i = 0; $i < 2; $i++) {
            $alpha_key .= $keys[array_rand($keys)];
        }
//print_r($alpha_key);exit;
        $length = $size - 2;

        $key = '';
        $keys = range(0, 9);

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $alpha_key . $key;
    }

}
if (!function_exists('err_404')) {

    function err_404() {
        $CI = & get_instance();
        $CI->load->view('err404');
        echo $CI->output->get_output();
        exit;
    }

}

if (!function_exists('encode_url')) {

    function encode_url($string, $key = "", $url_safe = TRUE) {
        if ($key == null || $key == "") {
            $key = "tyz_mydefaulturlencryption";
        }
        $CI = & get_instance();
        $ret = $CI->encrypt->encode($string, $key);

        if ($url_safe) {
            $ret = strtr(
                    $ret, array(
                '+' => '.',
                '=' => '-',
                '/' => '~'
                    )
            );
        }

        return $ret;
    }

}
if (!function_exists('decode_url')) {

    function decode_url($string, $key = "") {
        if ($key == null || $key == "") {
            $key = "tyz_mydefaulturlencryption";
        }
        $CI = & get_instance();
        $string = strtr(
                $string, array(
            '.' => '+',
            '-' => '=',
            '~' => '/'
                )
        );

        return $CI->encrypt->decode($string, $key);
    }

}
if (!function_exists('Pagination')) {

    function Pagination($data, $limit = null, $current = null, $adjacents = null) {
        $result = array();

        if (isset($data, $limit) === true) {
            $result = range(1, ceil($data / $limit));

            if (isset($current, $adjacents) === true) {
                if (($adjacents = floor($adjacents / 2) * 2 + 1) >= 1) {
                    $result = array_slice($result, max(0, min(count($result) - $adjacents, intval($current) - ceil($adjacents / 2))), $adjacents);
                }
            }
        }

        return $result;
    }

}
if (!function_exists('convertNumber')) {

    function convertNumber($string) {

        $arabic = array("٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩");
        //pri($arabic);
        $num = range(0, 9);
        $englishNumbersOnly = str_replace($arabic, $num, $string);

        return $englishNumbersOnly;
    }

}
if (!function_exists('handleParams')) {

    function handleParams($params_arr) {
        $params = '';
        if (!empty($params_arr)) {
            foreach ($params_arr as $key => $value) {
                if (!empty($value)) {
                    $param_value = implode('-', $value);
                    $params .= "$key=$param_value&";
                }
            }
        }
        return trim($params, '&');
    }

}
if (!function_exists('isAssoc')) {

    function isAssoc(array $arr) {
//        if (array() === $arr)
//            return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

}
if (!function_exists('validate_segment')) {

    function validate_segment($segment, $rules, $site_url = TRUE) {
        $CI = &get_instance();
        $CI->load->library('form_validation');
        //$data = $CI->uri->segment($segment);
        $data = $segment;


        $rules = explode('|', $rules);

        if (!in_array('required', $rules) && $data == '') {
            return $data;
        }

        if (in_array('required', $rules) and $data == '') {
            //set_message('The url has missing data.', 'error');

            if ($site_url) {
                $site_url = is_string($site_url) ? $site_url : '';
                redirect($site_url);
            }
        }

        //
        // Cycle through each rule and run it:
        foreach ($rules as $rule) {
            if ($rule == 'required') {
                continue;
            }

            // Is the rule a callback?
            $callback = FALSE;
            if (substr($rule, 0, 9) == 'callback_') {
                $rule = substr($rule, 9);
                $callback = TRUE;
            }

            //
            // Strip the parameter (if exists) from the rule
            // Rules can contain a parameter: max_length[5]
            $match = NULL;
            $param = FALSE;
            if (preg_match('/(.*?)\[(.*?)\]/', $rule, $match)) {
                $rule = $match[1];
                $param = $match[2];
            }

            //
            // Call the function that corresponds to the rule:
            if ($callback === TRUE) {
                if (!method_exists($CI, $rule)) {
                    $validates = FALSE;
                }

                if ($param) {
                    $result = $CI->$rule($data, $param);
                } else {
                    $result = $CI->$rule($data);
                }
            } else {
                if (!method_exists($CI->form_validation, $rule)) {
                    // If our own wrapper function doesn't exist,
                    // see if a native PHP function exists.
                    // Users can use any native PHP function
                    // call that has one param:
                    if (function_exists($rule)) {
                        if ($param) {
                            $result = $rule($data, $param);
                        } else {
                            $result = $rule($data);
                        }
                    }
                } else {
                    if ($param) {
                        $result = $CI->form_validation->$rule($data, $param);
                    } else {
                        $result = $CI->form_validation->$rule($data);
                    }
                }
            }

            //
            // Is it a bool or did the function change the data
            // and send it back:
            $data = (is_bool($result)) ? $data : $result;
//pri($data);
            //
            // Did the rule test negatively? If so, grab the error:
            if (is_bool($result) && $result === FALSE) {
                if ($site_url) {
                    $site_url = is_string($site_url) ? $site_url : '';
                    //set_message('The url has incorrect data.', 'error');
                    redirect($site_url);
                }

                return $data;
            }
        }

        return $data;
    }

}

if (!function_exists('getAddress3')) {

    /**
     * @param integer $lat
     * @param integer $lng
     * @param string $lang
     * @return string
     */
    function getAddress($lat, $lng, $lang = "AR") {
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($lat) . ',' . trim($lng) . '&language=' . $lang . '&sensor=false';
        $json = @file_get_contents($url);
        $data = json_decode($json);
        if ($data != null) {
            $status = $data->status;
            if ($status == "OK") {
                return $data->results[0]->formatted_address;
            } else {
                return "";
            }
        } else {
            return "";
        }
    }

}
if (!function_exists('getAddress2')) {

    /**
     * @param integer $lat
     * @param integer $lng
     * @param string $lang
     * @return string
     */
    function getAddress2($lat, $lng, $lang = "AR") {
        $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($lat) . ',' . trim($lng) . '&language=' . $lang . '&sensor=false';
        $json = @file_get_contents($url);
        $data = json_decode($json);
        $add_array = $data->results[0];
        $add_array = $add_array->address_components;

        foreach ($add_array as $key) {
            if ($key->types[0] == 'administrative_area_level_2') {
                $city = $key->long_name;
            }
            if ($key->types[0] == 'administrative_area_level_1') {
                $state = $key->long_name;
            }
            if ($key->types[0] == 'country') {
                $country = $key->long_name;
            }
        }
        return $add_array;
        if ($data != null) {
            $status = $data->status;
            if ($status == "OK") {
                return $data->results[0]->formatted_address;
            } else {
                return "";
            }
        } else {
            return "";
        }
    }

}

function sendNoti($tokens, $message, $title = null, $deviceType) {

    fcmCurl(fcmFields($tokens, $message, $title, $deviceType));
}

if (!function_exists('fcmFields')) {

    /**
     * @param string $tokens
     * @param string $message
     * @param string $title
     * @param integer $deviceType
     * @param integer $notificationType
     * @param integer $notificationId
     * @return string
     */
    function fcmFields($tokens, $message, $title = null, $deviceType) {
        if ($title == null)
            $title = config("app.name");

        $tokens = checkTokenArray($tokens);
        // unset($tokens);
        //$tokens = array();
        //$tokens[0] = "eATxcXYLWDU:APA91bHJIsdjDdX7pQLxzxxRXxzaOtTHG20KykNZr2liEfJxJvEI9e2OFxxAHMyBZ0GX21ULjIjDa7nk7Yg_VOZkK4XqddmYdMpFw1OuLPcM1zcYXmurZfaV5uJXOQaVmc2FCdNx32qk";
        if ($deviceType == 1) {
            $fields = array
                (
                'registration_ids' => $tokens,
                'data' => array(
                    'title' => $title,
                    'body' => $message,
                )
            );
        } else {
            $fields = array
                (
                'registration_ids' => $tokens,
                'notification' => array(
                    'title' => $title,
                    'body' => $message,
                    'sound' => 1,
                    'priority' => "high",
                    'vibrate' => 1
                )
            );
        }

        return json_encode($fields);
    }

}

if (!function_exists('checkTokenArray')) {

    /**
     * @param array|string $tokens
     * @return array
     */
    function checkTokenArray($tokens) {
        $returned = array();
        if (isset($tokens[0]) && is_array($tokens)) {

            return $tokens;
        } else {
            $returned[0] = $tokens;
            return $returned;
        }
    }

}

if (!function_exists('fcmCurl')) {

    /**
     * @param string $fields
     * @return bool
     */
    function fcmCurl($fields) {
        $returned = true;

        $url = 'https://fcm.googleapis.com/fcm/send';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, fcmHeaders());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        $result = curl_exec($ch);

        if ($result === False) {
            $returned = false;
            die('Curl Filed ' . curl_errno($ch));
        }
        curl_close($ch);
        return $returned;
    }

}
if (!function_exists('isBase64image')) {

    /**
     * @param string $fields
     * @return bool
     */
    function isBase64image($value) {
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
    }

}


if (!function_exists('curlRequest')) {

    /**
     * @param string $fields
     * @return bool
     */
    function curlRequest($url) {
        $returned = true;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);

        curl_close($ch);
        return $result;
    }

}

if (!function_exists('fcmHeaders')) {

    /**
     * @return array
     */
    function fcmHeaders() {
        $key = "AAAA2TgVDuk:APA91bE8cKwtxKTYSNyYGHFO3PZ3u-7qf0R1yi4fSjFkGjr7SYF2GoH5M0sMtIf9nYXxMc719i-pgOwTtZ4LLNDMvuLow4aqRkdahvPEVTEC8A1uVOjfX1rePu-XSLjbASrYezqXxt4m";

        $headers = array
            (
            'Authorization: key=' . $key,
            'Content-Type: application/json'
        );
        return $headers;
    }

}
if (!function_exists('unique_random')) {

    /**
     *
     * Generate a unique random string of characters
     * uses str_random() helper for generating the random string
     *
     * @param     $table - name of the table
     * @param     $col - name of the column that needs to be tested
     * @param int $chars - length of the random string
     *
     * @return string
     */
    function unique_random($table, $col, $chars = 16) {

        $unique = false;

        // Store tested results in array to not test them again
        $tested = [];

        do {

            // Generate random string of characters
            $random = str_random($chars);

            // Check if it's already testing
            // If so, don't query the database again
            if (in_array($random, $tested)) {
                continue;
            }

            // Check if it is unique in the database
            $count = DB::table($table)->where($col, '=', $random)->count();

            // Store the random character in the tested array
            // To keep track which ones are already tested
            $tested[] = $random;

            // String appears to be unique
            if ($count == 0) {
                // Set unique to true to break the loop
                $unique = true;
            }

            // If unique is still false at this point
            // it will just repeat all the steps until
            // it has generated a random string of characters
        } while (!$unique);


        return $random;
    }

}
if (!function_exists('getAddress')) {

    function getAddress($lat, $lng, $lang = "AR") {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim($lat) . ',' . trim($lng) . '&language=' . $lang . "&key=AIzaSyDGbxcCdO2kOPtbiHIHz4CpdzG30c2A6is";
        $data = json_decode(curlRequest($url));
        isset($data->status) ? $status = $data->status : $status = "FAIL";

        if ($status == "OK") {
            return $data->results[0]->formatted_address;
        } else {
            return "";
        }
    }

}


/**
 * @param $url
 * @return bool
 */
