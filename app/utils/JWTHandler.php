<?php
require_once 'vendor/autoload.php'; 
require_once 'app/config/JWTConfig.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler {

    public static function encode($data) {
        $payload = array_merge($data);
        $encode = JWT::encode($payload, JWTConfig::getKey(), 'HS256');
        return $encode;
    }

    public static function decode($token) {
        try {
            $decode = JWT::decode($token, new Key(JWTConfig::getKey(), 'HS256'));
            return $decode;
        } catch (Exception $e) {
            return null;
        }
    }
}


?>