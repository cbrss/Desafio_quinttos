<?php

class JWTConfig {
    private static $KEY = 'cacho';
    private static $ISSUER = 'http://localhost';
    private static $AUDIENCE = 'http://localhost';
    private static $EXPIRATION_TIME = '3600';

    public static function getKey() {
        return self::$KEY;
    }
    public static function getIssuer() {
        return self::$ISSUER;
    }
    public static function getAudience() {
        return self::$AUDIENCE;
    }
    public static function getExpirationTime() {
        return self::$EXPIRATION_TIME;
    }
}


?>