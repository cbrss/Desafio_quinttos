
<?php

require_once "vendor/autoload.php";
require_once "app/config/jwt.php";
require_once "app/models/userModel.php";
require_once "app/utils/JWTHandler.php";

class authController {
    private $userModel;

    public function __construct($userModel) {
        $this->userModel = $userModel;
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (empty($data['username']) || empty($data['password'])) {
            ResponseHttp::status400("Missing fields");
            return;
        }

        try {
            $user = $this->userModel->findByUsername($data['username']);
            if (!$user) {
                throw new Exception("User not found");
            }
            if (!password_verify($data['password'], $user['password'])){
                throw new Exception("Invalid password");
            } 
            $expirationTime = time() + EXPIRATION_TIME;

            $token = JWTHandler::encode([
                "iss" => ISSUER,
                "aud" => AUDIENCE,
                "exp" => $expirationTime,
                "data" => [
                    "id" => $user['id'],
                    "username" => $user['username']
                ]
            ]);
            setcookie('authToken', $token, [
                'expires' => $expirationTime,
                'path' => '/'
            ]);

            
    
            ResponseHttp::status200("Login successful", [
                "token" => $token,
            ]);
            
        } catch (Exception $e) {
            if ($e->getMessage() === "User not found" || $e->getMessage() === "Invalid password") {
                ResponseHttp::status401("Invalid credentials");
            } else {
                ResponseHttp::status500("Database error: " . $e->getMessage());
            }
        }

        return;
    }
}

?>