<?php

class DBConfig {
    private static $HOST = 'database-1.cd240i6sy1in.sa-east-1.rds.amazonaws.com';
    private static $NAME = 'quinttos_db';
    private static $USER = 'admin';
    private static $PASS = 'Cacho1234';
    private static $PORT = '3306';

    public static function getHost() {
        return self::$HOST;
    }
    public static function getName() {
        return self::$NAME;
    }
    public static function getUser() {
        return self::$USER;
    }
    public static function getPass() {
        return self::$PASS;
    }
    public static function getPort() {
        return self::$PORT;
    }
}

?>

