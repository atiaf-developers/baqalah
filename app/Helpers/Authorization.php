<?php

namespace App\Helpers;

use App\Models\User;

class AUTHORIZATION {

    public static function validateToken($token) {
        $key = 'ingDLMRuGe9UKHRNjs7cYckS2yul4lc3';
        $algorithm ='HS256';
        return JWT::decode($token, $key, array($algorithm));
    }

    public static function generateToken($data) {
        $key = 'ingDLMRuGe9UKHRNjs7cYckS2yul4lc3';
        $algorithm ='HS256';
        return JWT::encode($data, $key);
    }

    public static function tokenIsExist($headers) {
        return (array_key_exists('Authorization', $headers) && !empty($headers['Authorization']));
    }
    
    

}
