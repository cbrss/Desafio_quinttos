<?php

$requestUri = trim($_SERVER['REQUEST_URI'], '/');

if ($requestUri === '') { 
    header("Location: /users/home");
    return;
}
else if (strpos($requestUri, 'tasks') === 0) {
    header('Content-Type: application/json; charset=UTF-8');

    require_once './app/routes/taskRoute.php';
    return;
}
else if (strpos($requestUri, 'users') === 0) {
    header('Content-Type: application/json; charset=UTF-8');

    require_once './app/routes/userRoute.php';
    return;
}

?>
