<?php

class ResponseHttp {
    private static function sendResponse($statusCode, $success, $message, $data = []){
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=UTF-8');
        $response = array_merge(["success" => $success, "message" => $message], $data);
        echo json_encode($response);
        return;
    }

    public static function status200($message, $data = []) {
        self::sendResponse(200, true, $message, $data);
    }
    public static function status201($message = 'Resource created') {
        self::sendResponse(201, true, $message);
    }
    public static function status400($message = 'Incorrect request') {
        self::sendResponse(400, false, $message);
    }
    public static function status401($message = 'Unauthorized request') {
        self::sendResponse(401, false, $message);
    }
    public static function status403($message = 'Forbidden request') {
        self::sendResponse(403, false, $message);
    }
    public static function status404($message = 'Resource not found') {
        self::sendResponse(404, false, $message);
    }
    public static function status409($message = 'Conflict') {
        self::sendResponse(409, false, $message);
    }
    public static function status500($message = 'Internal server error') {
        self::sendResponse(500, false, $message);
    }
}
?>