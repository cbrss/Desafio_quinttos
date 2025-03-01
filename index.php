<?php

$requestUri = trim($_SERVER['REQUEST_URI'], '/');

if ($requestUri === '') { 
    require_once './app/routes/taskRoute.php';
    exit;
}

else if (strpos($requestUri, 'tasks') === 0) {
    header('Content-Type: application/json');

    require_once './app/routes/taskRoute.php';
    exit;
}

?>
