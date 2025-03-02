<?php

require_once 'app/models/userModel.php';
require_once 'app/views/userView.php';
require_once 'app/utils/ResponseHttp.php';

class UserController {
    private $userModel;
    private $userView;

    private static $validateUsername = '/^[a-zA-Z0-9]{3,20}$/'; 
    private static $validatePassword = '/^.{6,}$/';

    public function __construct($userModel, $userView) {
        $this->userModel = $userModel;
        $this->userView = $userView;
    }

    public function showLoginPage() {
        $this->userView->renderLogin();
    }

    public function find($id) {
        try {
            $user = $this->userModel->find($id);
            if ($user) {
                $userDTO = new userModelDTO($user['id'], $user['username']);
                ResponseHttp::status200("User found", ["user" => $userDTO]);
            } else {
                ResponseHttp::status404("User not found");
            }
        } catch (Exception $e) {
            ResponseHttp::status500("Database error: " . $e->getMessage());
        }
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (empty($data['username']) || empty($data['password'])) {
            ResponseHttp::status400("Missing fields");
            return;
        }
    
        try {
            # buscar user
            # comparar contrasenas
            $user = $this->userModel->findByUsername($data['username']);
            
            if (!$user) {
                throw new Exception("User not found");
            }
            error_log(json_encode($user));
            if ($user['password'] != $data['password']) {
                throw new Exception("Invalid password");
            } 
            session_start();
    
            $_SESSION['userId'] = $user['id'];
            $_SESSION['username'] = $user['username'];
    
            ResponseHttp::status200("Login successful", [
                "user" => $user['username'],
                "redirect" => "/tasks/list?userId=" . urlencode($user['id'])
            ]);
            
        } catch (Exception $e) {
            error_log("Parsed Request Path: " . json_encode($e)); 

            if ($e->getMessage() === "User not found" || $e->getMessage() === "Invalid password") {
                ResponseHttp::status401("Invalid credentials");
            } else {
                ResponseHttp::status500("Database error: " . $e->getMessage());
            }
        }
    }
    

    public function register() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$this->isValidRegisterData($data)) {
            return;
        }

        try {
            $existingUser = $this->userModel->findByUsername($data['username']);
            if ($existingUser) {
                ResponseHttp::status409("user already registered"); 
                return;
            }
    
            $this->userModel->register($data['username'], $data['password']);
            
            ResponseHttp::status201("User registered successfully", [
                "success" => true,
                "redirect" => "/users/login"
            ]);
        } catch (Exception $e) {
            ResponseHttp::status500("Database error: " . $e->getMessage());
        }
    }

    private function isValidRegisterData($data) {
        if (empty($data['username']) || empty($data['password'])) {
            ResponseHttp::status400("Missing fields");
            return false;
        }

        if (!preg_match(self::$validateUsername, $data['username'])) {
            ResponseHttp::status400("Invalid username, must be 3-20 characters long & contain only letters and numbers");
            return false;
        }

        if (!preg_match(self::$validatePassword, $data['password'])) {
            ResponseHttp::status400("Invalid password, must be at least 6 characters long");
            return false;
        }

        return true;
    }
}
?>
