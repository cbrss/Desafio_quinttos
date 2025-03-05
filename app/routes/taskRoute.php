<?php

require_once 'app/controllers/taskController.php';
require_once 'app/database/MySQLDatabase.php';
require_once 'app/config/DBConfig.php';
require_once 'app/views/taskView.php';
require_once 'app/utils/ResponseHttp.php';

$token = $_COOKIE['authToken'] ?? null;
if (!$token) {
    $headers = getallheaders();
    $authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : "";

    if (preg_match("/Bearer\s(\S+)/", $authHeader, $matches)) {
        $token = $matches[1];
    }
}
if (!$token) {
    ResponseHttp::status401("Unauthorized token");
    return;
}
$payload = JWTHandler::decode($token);
if (!$payload) {
    ResponseHttp::status403("Invalid token");
    return;
}

$DB_HOST = DBConfig::getHost();
$DB_NAME = DBConfig::getName();
$DB_USER = DBConfig::getUser();
$DB_PASS = DBConfig::getPass();
$DB_PORT = DBConfig::getPort();

$database = new MySQLDatabase($DB_HOST, $DB_NAME, $DB_USER, $DB_PASS, $DB_PORT);
$taskModel = new TaskModel($database);
$taskView = new TaskView();
$taskController = new TaskController($taskModel, $taskView);

$parsedUrl = parse_url($_SERVER['REQUEST_URI']); 
$requestPath = trim($parsedUrl['path'], '/'); 

$requestUri = explode('/', $requestPath);
$method = $_SERVER['REQUEST_METHOD'];

if ($requestUri[0] === 'tasks') {
    switch ($method) {
        case 'GET':
            if (isset($requestUri[1])) {
                if ($requestUri[1] == 'list') {
                    $taskController->homeTask($payload->data);
                } elseif ($requestUri[1]) {
                    $taskController->find($requestUri[1], $payload->data);
                } else {
                    HttpResponse::status404("Path not found");
                }
            }
            break;
        case 'POST':
            $taskController->save($payload->data);
            break;
        case 'PUT':
            $taskController->edit();
            break;
        case 'DELETE':
            $taskController->delete();
            break;
        default:
            HttpResponse::status404("Path not found");
    }
} else {
    HttpResponse::status404("Path not found");
}
?>
