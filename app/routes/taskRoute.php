<?php

require_once 'app/controllers/taskController.php';
require_once 'app/database/MySQLDatabase.php';
require_once 'app/config/config.php';
require_once 'app/views/taskView.php';

$database = new MySQLDatabase(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
$taskModel = new TaskModel($database);
$taskView = new TaskView();
$taskController = new TaskController($taskModel, $taskView);

$parsedUrl = parse_url($_SERVER['REQUEST_URI']); 
$requestPath = trim($parsedUrl['path'], '/'); 

$requestUri = explode('/', $requestPath);
$method = $_SERVER['REQUEST_METHOD'];

if ($requestUri[0] === 'tasks' && isset($requestUri[1]) && $requestUri[1] === 'list') {
    $taskController->list();
    exit;
}

if ($requestUri[0] === 'tasks') {
    switch ($method) {
        case 'GET':

            if (isset($requestUri[1])) {
                if ($requestUri[1] == 'list') {
                    
                    $taskController->list();
                } elseif ($requestUri[1]) {
                    $taskController->find($requestUri[1]);
                } else {
                    $taskController->findAll();
                }
            }
            break;
        case 'POST':
            $taskController->save();
            break;
        case 'PUT':
            $taskController->edit($requestUri[1]);
            break;
        case 'DELETE':
            $taskController->delete($requestUri[1]);
            break;
        default:
            echo json_encode(["success" => false, "message" => "error"]);
    }
}
else {
    echo json_encode(["success" => false, "message" => "Error: path not found"]);
}
?>
