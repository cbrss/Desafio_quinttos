<?php


require_once 'app/models/taskModel.php';
require_once 'app/views/taskView.php';
require_once 'app/utils/ResponseHttp.php';
require_once 'app/models/taskModelDTO.php';

class TaskController {
    private $taskModel;
    private $taskView;

    private static $validateTaskTitle= '/^[a-zA-Z0-9]{1,255}$/'; 
    private static $validateTaskDescription = '/^.{1,255}$/s';

    public function __construct($taskModel, $taskView) {
        $this->taskModel = $taskModel;
        $this->taskView = $taskView;
    }

    public function list() {
        session_start(); 

        if (!isset($_SESSION['userId'])) {
            ResponseHttp::status401("Unauthorized: You must log in");
            return;
        }
        $sessionUserId = $_SESSION['userId'];
        $urlUserId = isset($_GET['userId']) ? intval($_GET['userId']) : null;
        if ($urlUserId !== $sessionUserId) {
            ResponseHttp::status403("Forbidden: You do not have permission to access this resource");
            return;
        }

        try {
            $userId = $_SESSION['userId']; 
            $userName = $_SESSION['username'];

            $tasks = $this->taskModel->findAllByUserId($userId);
            $tasksDTO = array_map(function($task) {
                return new TaskModelDTO($task['id'], $task['title'], $task['description'], $task['status']);
            }, $tasks);

            $tasksDTO = array_filter($tasksDTO, function($taskDTO) {
                return $taskDTO->status != 'deleted';
            });
    
            $this->taskView->renderList($tasksDTO, $userName);
        } catch (Exception $e) {
            ResponseHttp::status500("Error: " . $e->getMessage());
        }
    }

    public function findAll() {
        try {
            $tasks = $this->taskModel->findAll();
            $tasksDTO = array_map(function($task) {
                return new TaskModelDTO($task['id'], $task['title'], $task['description'], $task['status']);
            }, $tasks);
            ResponseHttp::status200("Tasks retrieved", ["tasks" => $tasksDTO]);
        } catch (Exception $e) {
            ResponseHttp::status500("Database error: " . $e->getMessage());
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

    public function save() { 
        $data = json_decode(file_get_contents("php://input"), true);
        session_start();

        if (!isset($_SESSION['userId'])) {
            ResponseHttp::status401("Unauthorized: You must log in");
            return;
        }

        if (empty($data['title']) || empty($data['description'])) {
            ResponseHttp::status400("Missing fields");
        } else if(!preg_match(self::$validateTaskTitle, $data['title'])) {
            ResponseHttp::status400("Invalid title, must be 1-255 characters long & contain only numbers and letters");
        } else if (!preg_match(self::$validateTaskDescription, $data['description'])) {
            ResponseHttp::status400("Invalid description, must be 1-255 characters long");
        } else {
            try{
                $userId = $_SESSION['userId'];
                $this->taskModel->save($data['title'], $data['description'], $userId);
                ResponseHttp::status201("Task created");
            } catch (Exception $e) {
                ResponseHttp::status500("Database error: " . $e->getMessage());
            }

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
}
?>