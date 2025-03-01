<?php

require_once 'controllers/taskController.php';
require_once 'database/MySQLDatabase.php';
require_once 'config/config.php';
require_once 'views/taskView.php';

$database = new MySQLDatabase(DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT);
$taskModel = new TaskModel($database);
$taskView = new TaskView();
$taskController = new TaskController($taskModel, $taskView);

$requestUri = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$method = $_SERVER['REQUEST_METHOD'];


if ($requestUri[0] === '' || $requestUri[0] === 'index.php') {
    $taskController->index();
    exit;
}

if ($requestUri[0] === 'tasks') {
    switch ($method) {
        case 'GET':
            isset($requestUri[1]) ? $taskController->find($requestUri[1]) : $taskController->findAll();
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
    echo json_encode(["success" => false, "message" => "Error: ruta no encontrada"]);
}
?>
