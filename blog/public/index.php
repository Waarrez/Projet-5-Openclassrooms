<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Zitro\Blog\Controllers\HomeController;
use Doctrine\ORM\Tools\Console\ConsoleRunner;


// Configuration de Doctrine ORM

$config = Setup::createAnnotationMetadataConfiguration(
    [dirname(__DIR__) . '/src/Entity'],
    true,
    null,
    null,
    false
);

// Autres configurations de Doctrine ORM...
// ...

// Création de l'EntityManager
$connectionOptions = [
    'driver' => 'pdo_mysql',
    'host' => 'localhost',
    'port' => 3307,
    'dbname' => 'blog_php',
    'user' => 'root',
    'password' => '',
];

try {
    $entityManager = EntityManager::create($connectionOptions, $config);
} catch (\Doctrine\ORM\Exception\ORMException $e) {
    dump($e->getMessage());
}

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('GET', '/articles', [HomeController::class, 'articles']);
    $r->addRoute('GET', '/login', [HomeController::class, 'login']);
    $r->addRoute('GET', '/register', [HomeController::class, 'register']);
    $r->addRoute('GET', '/test', [HomeController::class, 'test']);
});

$httpMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_SPECIAL_CHARS);
$uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_SPECIAL_CHARS);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

$handler = $routeInfo[1];
$vars = $routeInfo[2];

$controller = $handler[0];
$method = $handler[1];

$controllerObject = new $controller($entityManager);
$controllerObject->$method($vars);
