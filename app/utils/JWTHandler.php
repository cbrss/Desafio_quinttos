<?php
require_once 'vendor/autoload.php'; 
require_once 'app/config/jwt.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler {

    public static function encode($data) {
        $payload = array_merge($data, ["exp" => time() + EXPIRATION_TIME]);
        $encode = JWT::encode($payload, KEY_JWT, 'HS256');
        return $encode;
    }

    public static function decode($token) {
        try {
            $decode = JWT::decode($token, new Key(KEY_JWT, 'HS256'));
            return $decode;
        } catch (Exception $e) {
            return null;
        }
    }
}


?>