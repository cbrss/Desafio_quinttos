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
        
        try {
            $tasks = $this->taskModel->findAll();
            ResponseHttp::status200("Tasks retrieved", ["tasks" => $tasks]);
        } catch (Exception $e) {
            ResponseHttp::status500("Db error: " . $e->getMessage());
            return;
        }
        return;
    }

    public function find($id) {
        
        try {
            $task = $this->taskModel->find($id);
            if ($task) {
                ResponseHttp::status200("Task found", ["tasks" => $task]);
            } else {
                ResponseHttp::status404("Task doesn't exist");
            }
        } catch (Exception $e) {
            ResponseHttp::status500("Database error: " . $e->getMessage());
            return;
        }
        return;
    }

    public function save() {
        
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['title']) || empty($data['description'])) {
            ResponseHttp::status400("Missing fields");
        } else if(!preg_match(self::$validateTaskTitle, $data['title'])) {
            ResponseHttp::status400("Invalid title, must be 1-255 characters long & contain only numbers and letters");
        } else if (!preg_match(self::$validateTaskDescription, $data['description'])) {
            ResponseHttp::status400("Invalid description, must be 1-255 characters long");
        } else {
            try{
                $this->taskModel->save($data['title'], $data['description']);
            } catch (Exception $e) {
                ResponseHttp::status500("Database error: " . $e->getMessage());
                return;
            }

            ResponseHttp::status201("Task created");
        }

        return;
    }

    public function edit($id) {
        
        $data = json_decode(file_get_contents("php://input"), true);
        $task = NULL;
        try{
            $task = $this->taskModel->find($id);
        } catch (Exception $e) {
            ResponseHttp::status500("Database error: " . $e->getMessage());
            return;
        }
        if (!$task) {
            ResponseHttp::status404("Task doesn't exist");
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
            ResponseHttp::status500("Database error: " . $e->getMessage());
            return;
        }
        ResponseHttp::status200("Task updated");
        return;
    }

    public function delete($id) {
        

        try {
            $task = $this->taskModel->find($id);
            if (!$task) {
                ResponseHttp::status404("Task doesn't exist");
            }

            $this->taskModel->delete($id);
        } catch (Exception $e) {
            ResponseHttp::status500("Database error: " . $e->getMessage());
            return;
        }
        ResponseHttp::status200("Task deleted");
        return;
    }
}
?>