<?php

require_once 'app/models/taskModel.php';
require_once 'app/views/taskView.php';
require_once 'app/utils/ResponseHttp.php';

class TaskController {
    private $taskModel;
    private $taskView;

    private static $validateTaskTitle= '/^[a-zA-Z0-9]{1,255}$/'; 
    private static $validateTaskDescription = '/^.{1,255}$/s';

    public function __construct($taskModel, $taskView) {
        $this->taskModel = $taskModel;
        $this->taskView = $taskView;
    }

    public function index() {
        try{
            $tasks = $this->taskModel->findAll();
            $tasks = array_filter($tasks, function($task) {
                return $task->status != 'deleted';
            });
            
            $this->taskView->render($tasks);
        } catch (Exception $e) {
            echo "error" . $e->getMessage();
            return;
        }
        #$this->taskView->renderError();
        return;
    }

    public function findAll() {
        header('Content-Type: application/json');
        try {
            $tasks = $this->taskModel->findAll();
            $response = ResponseHttp::status200("Tasks retrieved");
            $response["tasks"] = $tasks;
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode(ResponseHttp::status500("Db error: " . $e->getMessage()));
            return;
        }
        return;
    }

    public function find($id) {
        header('Content-Type: application/json');
        $task = "";
        try {
            $task = $this->taskModel->find($id);
            if ($task) {
                $response = ResponseHttp::status200("Task found");
                $response["task"] = $task;
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(ResponseHttp::status500("Task aadoesn't exist"));
            }
        } catch (Exception $e) {
            echo json_encode(ResponseHttp::status500("Db error: " . $e->getMessage()));
            return;
        }
        return;
    }

    public function save() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['title']) || empty($data['description'])) {
            echo json_encode(ResponseHttp::status400("Missing fields"));
        } else if(!preg_match(self::$validateTaskTitle, $data['title'])) {
            echo json_encode(ResponseHttp::status400("Invalid title, must be 1-255 characters long & contain only numbers and letters"));
        } else if (!preg_match(self::$validateTaskDescription, $data['description'])) {
            echo json_encode(ResponseHttp::status400("Invalid description, must be 1-255 characters long"));
        } else {
            try{
                $this->taskModel->save($data['title'], $data['description']);
            } catch (Exception $e) {
                echo json_encode(ResponseHttp::status500("Dba error: " . $e->getMessage()));
                return;
            }

            echo json_encode(ResponseHttp::status201("Task created"));
        }

        return;
    }

    public function edit($id) {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        $task = $this->taskModel->find($id);
        if (!$task) {
            echo json_encode(ResponseHttp::status404("Task doesn't exist"));
        }
        if (empty($data['title'])) {
            $data['title'] = $task->title;
        }
        if (empty($data['description'])) {
            $data['description'] = $task->description;
        }
        try{
            $this->taskModel->update($id, $data['status']);
        } catch (Exception $e) {
            echo json_encode(ResponseHttp::status500("Db error: " . $e->getMessage()));
            return;
        }
        echo json_encode(ResponseHttp::status200("Task updated"));
        return;
    }

    public function delete($id) {
        header('Content-Type: application/json');

        try {
            $task = $this->taskModel->find($id);
            if (!$task) {
                echo json_encode(ResponseHttp::status404("Task doesn't exist"));
            }

            $this->taskModel->delete($id);
        } catch (Exception $e) {
            echo json_encode(ResponseHttp::status500("Db error: " . $e->getMessage() ));
            return;
        }
        echo json_encode(ResponseHttp::status200("Task deleted"));
        return;
    }
}
?>