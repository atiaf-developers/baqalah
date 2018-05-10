<?php

/**
 * Created by PhpStorm.
 * User: ATIAF
 * Date: 05/02/2018
 * Time: 01:56 م
 */

namespace App\Helpers;

/**
 * Class Pusher
 * @package App\Helpers
 */
class Pusher {

    /** @var  \Pusher\Pusher */
    public $pusher;

    /** @var  array|string */
    public $data;
    private $appKey;
    private $appSecret;
    private $appId;
    private $cluster;

    /**
     * Pusher constructor.
     * @param array|string $data
     */
    public function __construct() {
        $this->init();
    }

    private function init() {
        $this->appId = env("PUSHER_APP_ID");
        $this->appSecret = env("PUSHER_APP_SECRET");
        $this->appKey = env("PUSHER_APP_KEY");
        $this->cluster = env("PUSHER_CLUSTER");
        $this->pusher = new \Pusher\Pusher($this->appKey, $this->appSecret, $this->appId
                , array('cluster' => $this->cluster, 'encrypted' => true
            , 'curl_options' => array(CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4)));
    }


    /**
     * @param $data
     * @return bool
     */
    public function trigger($channel,$event,$data) {
        return $this->pusher->trigger($channel, $event,$data);

    }

}
