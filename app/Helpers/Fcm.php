<?php

namespace App\Helpers;

class Fcm {

    private $noti_url;
    private $key = array();
    // codeigniter instance
    private $_ci;
    private $_format = 'json';
    private $_json_format = 'assoc';
    // debug mode
    private $_enable_debug = false;
    // http request
    private $_request_url;
    private $_request_headers;
    private $_last_url;
    private $_request_body;
    // http reponse
    private $_http_status;
    private $_http_response;
    // curl init session
    protected $session;
    protected $options = [];
    protected $url;

    public function __construct() {
        $this->key = 'AAAA7NJWmTI:APA91bHtx_jGllbSgbkvnBVBSX2TaPb5A3Iufo0WEkKgfbvUPWasu_NSrahVhSB9BoiKSMNHWk9nV6CThRUU4bPDtuqnsTtw9Ec_Gk3GEWfhLzWl3MmswJIz8XQFJLH1Rbm5mNY_4U5o';
        $this->noti_url = 'https://fcm.googleapis.com/fcm/send';
    }

    /**
     * sending an SMS message
     *
     * @param string
     * @param string
     * @param array
     * @param string (text, binary or wappush)
     * return string
     */
    public function send2($token, $notification, $device_type) {
        mb_internal_encoding("UTF-8");
        mb_http_output("UTF-8");

        $options = [
            CURLOPT_POST => TRUE,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                'Authorization: key=' . $this->key,
                'Content-Type: application/json; charset=utf-8'
            ),
        ];

        $data = array('vibrate' => 0, 'sound' => 'disabled', 'alert' => 0);
        foreach ($notification as $key => $value) {
            $data[$key] = $value;
        }
   
        if ($device_type == 'ios') {
            $params = array(
                'to' => $token,
                'notification' => $data,
                'priority' => 'high',
                'content_available' => true,
            );
        } else if ($device_type == 'and') {
            $params = array(
                'to' => $token,
                'data' => $data,
                'priority' => 'high',
                'content_available' => true,
                'sound' => 1,
                'vibrate' => 1
            );
        } else if ($device_type == 'twice') {
            $params = array(
                'to' => $token,
                'notification' => $data,
                'data' => $data,
            );
        }


        return $this->request('post', $this->noti_url, json_encode($params), $options);
    }

    public function send($token, $notification, $device_type) {
        mb_internal_encoding("UTF-8");
        mb_http_output("UTF-8");

        $options = [
            CURLOPT_POST => TRUE,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                'Authorization: key=' . $this->key,
                'Content-Type: application/json; charset=utf-8'
            ),
        ];

        $data = array('priority' => 'high', 'content_available' => true, 'vibrate' => 1, 'sound' => 1, 'alert' => 1);
        foreach ($notification as $key => $value) {
            $data[$key] = $value;
        }
        $params=array();
        if(is_array($token)){
            $params['registration_ids']=$token;
        }else{
            $params['to']=$token;
        }
        if ($device_type == 'ios') {
            /*$params = array(
                'notification' => $data,
            );*/
            $params['notification'] = $data;
        } else if ($device_type == 'and') {
            /*$params = array(
                'data' => $data,
            );*/
            $params['data'] = $data;
        } else if ($device_type == 'twice') {
            $params = array(
                'to' => $token,
                'notification' => $data,
                'data' => $data,
            );
            
        }
        //dd(json_encode($params,JSON_FORCE_OBJECT));

        return $this->request('post', $this->noti_url, json_encode($params,JSON_FORCE_OBJECT), $options);
    }

    public function send3($token, $notification, $device_type) {
        mb_internal_encoding("UTF-8");
        mb_http_output("UTF-8");

        $options = [
            CURLOPT_POST => TRUE,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                'Authorization: key=' . $this->key,
                'Content-Type: application/json; charset=utf-8'
            ),
        ];

//        $data = array('vibrate' => 0, 'sound' => 'disabled','alert' => 0);
//        foreach ($notification as $key => $value) {
//            $data[$key] = $value;
//        }
        if ($device_type == 'ios') {
            $params = array(
                'to' => $token,
                'notification' => $notification,
                'priority' => 'high',
                'content_available' => true,
                'sound' => 1,
                'vibrate' => 1
            );
        } else if ($device_type == 'and') {
            $params = array(
                'to' => $token,
                'data' => $notification,
                'priority' => 'high',
                'content_available' => true,
                'sound' => 1,
                'vibrate' => 1
            );
        } else if ($device_type == 'twice') {
            $params = array(
                'to' => $token,
                'notification' => $notification,
                'data' => $notification,
            );
        }


        return $this->request('post', $this->noti_url, json_encode($params), $options);
    }

    protected function request($method, $url, $params = [], $options = []) {
        if ($method === 'get') {
            $uri = $url . ($params ? '?' . http_build_query($params) : '');
            $this->create($uri);
            $this->_request_url = $uri;
        } else {
            //$data = $params ? http_build_query($params) : '';
            //return $url;
            $this->create($url);
            $this->_request_url = $url;

            $options[CURLOPT_POSTFIELDS] = $params;
            $this->_request_body = $options[CURLOPT_POSTFIELDS];
        }
        // TRUE to return the transfer as a string of the return value of curl_exec()
        // instead of outputting it out directly.
        $options[CURLOPT_RETURNTRANSFER] = true;
        $options[CURLINFO_HEADER_OUT] = true;
        $options[CURLOPT_SSL_VERIFYPEER] = false;
        $options[CURLOPT_SSL_VERIFYHOST] = false;
        $options[CURLOPT_FOLLOWLOCATION] = true;


        $this->options($options);

        return $this->execute();
    }

    protected function options($options = []) {
        // Set all options provided
        curl_setopt_array($this->session, $options);

        return $this;
    }

    protected function create($url) {

        $this->session = curl_init($url);

        return $this;
    }

    protected function execute() {
        // Execute the request & and hide all output
        $this->_http_response = curl_exec($this->session);
        $this->_http_status = curl_getinfo($this->session, CURLINFO_HTTP_CODE);
        $this->_request_headers = curl_getinfo($this->session, CURLINFO_HEADER_OUT);
        $this->_last_url = curl_getinfo($this->session, CURLINFO_EFFECTIVE_URL);

        curl_close($this->session);

        return $this->response();
    }

    /**
     *
     * get http response (json or xml)
     *
     * @return json or xml
     */
    protected function response() {
        switch ($this->_format) {
            case 'xml':
                $response_obj = $this->_http_response;

                break;
            case 'json':
            default: {
                    $is_assoc = ($this->_json_format === 'assoc') ? TRUE : FALSE;
                    $response_obj = json_decode($this->_http_response, $is_assoc);
                }
        }

        return $response_obj;
    }

    /**
     *
     * get http response status
     *
     * @return int
     */
    public function get_http_status() {
        return (int) $this->_http_status;
    }

    public function get_last_url() {
        return $this->_last_url;
    }

    public function get_request() {
        //return $this->_request_url . "\n" . trim($this->_request_headers) . "\n" . trim($this->_request_body);
        return trim($this->_request_headers) . "\n" . trim($this->_request_body);
    }

    public function get_request2() {

        $header_size = curl_getinfo($this->session, CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr($response, $header_size);
        $result['http_code'] = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $result['last_url'] = curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL);
        return $result;
    }

    public function pri($msg) {
        echo '<pre>';
        print_r($msg);
        echo '</pre>';
        exit;
    }

}

/* End of file nexmo.php */
/* Location: ./application/libraries/nexmo.php */
