<?php

class ResponseHttp {
    private static function sendResponse($statusCode, $success, $message, $data = []){
        http_response_code($statusCode);
        header('Content-Type: application/json');
        $response = array_merge(["success" => $success, "message" => $message], $data);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        return;
    }

    public static function status200($message, $data = []) {
        self::sendResponse(200, true, $message, $data);
    }

    public static function status201($message = 'Resource created') {
        http_response_code(201);
        self::sendResponse(201, true, $message);
    }

    public static function status400($message = 'Incorrect request') {
        self::sendResponse(400, false, $message);
    }

    public static function status404($message = 'Resource not found') {
        self::sendResponse(404, false, $message);
    }

    public static function status500($message = 'Internal server error') {
        self::sendResponse(200, false, $message);
    }
}
?>