<?php
require_once 'app/models/taskModel.php';
require_once 'app/views/taskView.php';
require_once 'app/utils/ResponseHttp.php';
require_once 'app/models/taskModelDTO.php';
require_once "app/config/jwt.php";
require_once "app/utils/JWTHandler.php";


class TaskController {
    private $taskModel;
    private $taskView;

    private static $validateTaskTitle= '/^[a-zA-Z0-9 ]{1,255}$/'; 
    private static $validateTaskDescription = '/^.{1,255}$/s';

    public function __construct($taskModel, $taskView) {
        $this->taskModel = $taskModel;
        $this->taskView = $taskView;
    }

    public function homeTask($user) {
        try {
            $tasks = $this->taskModel->findAllByUserId($user->id);
            $tasksDTO = array_map(function($task) {
                return new TaskModelDTO($task['id'], $task['title'], $task['description'], $task['status']);
            }, $tasks);

            $tasksDTO = array_filter($tasksDTO, function($taskDTO) {
                return $taskDTO->status != 'deleted';
            });

            $this->taskView->renderList($tasksDTO, $user->username);
        } catch (Exception $e) {
            ResponseHttp::status500("Error: " . $e->getMessage());
        }

        return;
    }

    public function find($id) {

        try {
            $task = $this->taskModel->find($id);
            if ($task) {
                $taskDTO = new TaskModelDTO($task['id'], $task['title'], $task['description'], $task['status']);
                ResponseHttp::status200("Task found", ["task" => $taskDTO]);
            } else {
                ResponseHttp::status404("Task doesn't exist");
            }
        } catch (Exception $e) {
            ResponseHttp::status500("Database error: " . $e->getMessage());
        }

        return;
    }

    public function save($user) { 
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$this->isValidTaskData($data)) {
            return;
        }

        try {
                $this->taskModel->save($data['title'], $data['description'], $user->id);
                ResponseHttp::status201("Task created");
        } catch (Exception $e) {
            ResponseHttp::status500("Database error: " . $e->getMessage());
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
            return;
        }

        if (!in_array($data['status'], ["completed", "in_progress"])) {
            ResponseHttp::status400("Invalid status, allowed values are: compelted, in_progress ");
            return;
        }

        try{
            $this->taskModel->updateStatus($id, $data['status']);
            ResponseHttp::status200("Task updated");
        } catch (Exception $e) {
            ResponseHttp::status500("Database error: " . $e->getMessage());
        }

        return;
    }

    public function delete($id) {
        try {
            $task = $this->taskModel->find($id);
            if (!$task) {
                ResponseHttp::status404("Task doesn't exist");
            }

            $this->taskModel->delete($id);
            ResponseHttp::status200("Task deleted");
        } catch (Exception $e) {
            ResponseHttp::status500("Database error: " . $e->getMessage());
        }

        return;
    }

    private function isValidTaskData($data) {
        if (empty($data['title'])) {
            ResponseHttp::status400("Title is required");
            return false;
        }

        if (isset($data['title']) && !preg_match(self::$validateTaskTitle, $data['title'])) {
            ResponseHttp::status400("Invalid title, must be 1-255 characters long & contain only numbers and letters");
            return false;
        }

        if (empty($data['description'])) {
            ResponseHttp::status400("Description is required");
            return false;
        }

        if (isset($data['description']) && !preg_match(self::$validateTaskDescription, $data['description'])) {
            ResponseHttp::status400("Invalid description, must be 1-255 characters long");
            return false;
        }

        return true;
    }
}
?>