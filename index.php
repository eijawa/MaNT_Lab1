<?php
require './bootstrap.php';
include './src/gateways/BasicGateway.php';
include './src/controllers/WindowController.php';
include './src/controllers/ServiceController.php';
include './src/controllers/QueueController.php';

// header("Access-Control-Allow-Origin: *");
// header("Content-Type: application/json; charset=UTF-8");
// header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
// header("Access-Control-Max-Age: 3600");
// header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

$allowed_uri = ["window", "service", "queue"];
$allow = false;

foreach ($allowed_uri as $u) {
    if (strcmp($uri[1], $u) === 0) $allow = true;
}

if (! $allow) {
    echo "Not allowed";
    header("HTTP/1.1 404 Not Found");
    exit();
}

// if ($uri[1] != "window" or $uri[1] != "service" or $uri[1] != "queue") {
//     header("HTTP/1.1 404 Not Found");
//     exit();
// }

$entityId = null;

if (isset($uri[2])) {
    $entityId = (int) $uri[2];
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch($uri[1]) {
    case 'window':
        $controller = new WindowController($dbConnection, $requestMethod, $entityId);
        $controller->processRequest();
        break;
    case 'service':
        $controller = new ServiceController($dbConnection, $requestMethod, $entityId);
        $controller->processRequest();
        break;
    case 'queue':
        $controller = new QueueController($dbConnection, $requestMethod, $entityId);
        $controller->processRequest();
        break;
    default:
        break;
}
?>