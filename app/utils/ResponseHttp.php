<?php

class ResponseHttp {
    public static function status200($message) {
        http_response_code(200);
        return ["success" => true, "message" => $message];
    }

    public static function status201($message = 'Resource created') {
        http_response_code(201);
        return ["success" => true, "message" => $message];
    }

    public static function status400($message = 'Incorrect request') {
        http_response_code(400);
        return ["success" => false, "message" => $message];
    }

    public static function status404($message = 'Not found') {
        http_response_code(404);
        return ["success" => false, "message" => $message];
    }

    public static function status500($message = 'Internal server error') {
        http_response_code(500);
        return ["success" => false, "message" => $message];
    }
}
?>