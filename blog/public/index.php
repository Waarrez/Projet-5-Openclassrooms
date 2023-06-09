<?php

require '../vendor/autoload.php';
use Zitro\Blog\Controllers\HomeController;

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

$handler = $routeInfo[1];
$vars = $routeInfo[2];

$controller = $handler[0];
$method = $handler[1];

$controllerObject = new $controller();
$controllerObject->$method($vars);

?>
