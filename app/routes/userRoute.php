<?php

require_once 'app/controllers/userController.php';
require_once 'app/database/MySQLDatabase.php';
require_once 'app/config/config.php';

$database = new MySQLDatabase(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
$userModel = new UserModel($database);
$userView = new UserView();
$userController = new UserController($userModel, $userView);

$parsedUrl = parse_url($_SERVER['REQUEST_URI']); 
$requestPath = trim($parsedUrl['path'], '/'); 

$requestUri = explode('/', $requestPath);
$method = $_SERVER['REQUEST_METHOD'];

if ($requestUri[0] === 'users') {
    switch ($method) {
        case 'GET':
            if (!isset($requestUri[1])) {
                echo json_encode(["success" => false, "message" => "User ID required"]);
            }
            if ($requestUri[1] === 'home') {
                $userController->showHomePage(); 
            } else {
                $userController->find($requestUri[1]);
            }
            break;
        case 'POST':
            if (!isset($requestUri[1])) {
                echo json_encode(["success" => false, "message" => "Invalid request"]);
            }
            if ($requestUri[1] === 'login') {
                $userController->login();
            } elseif ($requestUri[1] === 'register') {
                $userController->register(); 
            }

            break;
        default:
            echo json_encode(["success" => false, "message" => "Error: Invalid request"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Error: Path not found"]);
}

?>
