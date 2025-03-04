<?php

require_once 'app/controllers/userController.php';
require_once 'app/controllers/authController.php';
require_once 'app/database/MySQLDatabase.php';
require_once 'app/config/config.php';
require_once 'app/utils/ResponseHttp.php';

$database = new MySQLDatabase(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
$userModel = new UserModel($database);
$userView = new UserView();
$userController = new UserController($userModel, $userView);
$authController = new authController($userModel);

$parsedUrl = parse_url($_SERVER['REQUEST_URI']); 
$requestPath = trim($parsedUrl['path'], '/'); 

$requestUri = explode('/', $requestPath);
$method = $_SERVER['REQUEST_METHOD'];

if ($requestUri[0] === 'users') {
    switch ($method) {
        case 'GET':
            if (!isset($requestUri[1])) {
                HttpResponse::status404("Path not found");
            }
            if ($requestUri[1] === 'home') {
                $userController->showHomePage(); 
            } else {
                $userController->find($requestUri[1]);
            }
            break;
        case 'POST':
            if (!isset($requestUri[1])) {
                HttpResponse::status404("Path not found");
            }
            if ($requestUri[1] === 'login') {
                $authController->login();
            } elseif ($requestUri[1] === 'register') {
                $userController->register(); 
            }

            break;
        default:
            HttpResponse::status404("Path not found");
    }
} else {
    HttpResponse::status404("Path not found");
}

?>
