<?php

require_once 'models/taskModel.php';
require_once 'views/taskView.php';

class TaskController {
    private $taskModel;
    private $taskView;

    public function __construct($taskModel, $taskView) {
        $this->taskModel = $taskModel;
        $this->taskView = $taskView;
    }

    public function index() {
        $tasks = $this->taskModel->findAll();
        $tasks = array_filter($tasks, function($task) {
            return $task['status'] != 'deleted';
        });
        
        $this->taskView->render($tasks);
    }

    public function findAll() {
        header('Content-Type: application/json');

        echo json_encode(["success" => true, "tasks" => $this->taskModel->findAll()], JSON_UNESCAPED_UNICODE);
    }

    public function find($id) {
        header('Content-Type: application/json');

        $result = $this->taskModel->find($id);
        
        if ($result) {
            echo json_encode(["success" => true, "task" => $result]);
        } else {
            echo json_encode(["success" => false, "message" => "No existe la tarea."]);
        }
    }

    public function save() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['title']) || !isset($data['description'])) {
            echo json_encode(["success" => false, "message" => "Faltan datos"]);
            return;
        }

        $this->taskModel->save($data['title'], $data['description']);
        echo json_encode(["success" => true, "message" => "Tarea creada"]);
    }

    public function edit($id) {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!isset($data['title']) || !isset($data['description']) || !isset($data['status'])) {
            echo json_encode(["success" => false, "message" => "Faltan datos"]);
            return;
        }

        $this->taskModel->update($id, $data['status']);
        echo json_encode(["success" => true, "message" => "Tarea actualizada"]);
    }

    public function delete($id) {
        header('Content-Type: application/json');

        $this->taskModel->delete($id);
        echo json_encode(["success" => true, "message" => "Tarea eliminada"]);
    }
}
?>