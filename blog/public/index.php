<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Zitro\Blog\Controllers\HomeController;

// Inclure la classe BDD si elle n'est pas déjà incluse
require_once '../src/Classes/BDD.php';
require_once '../src/Classes/Mail.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "blog_php";

// Créer une instance de la classe BDD pour la connexion
$bdd = new BDD($servername, $username, $password, $dbname);
$mail = new Mail();

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('GET', '/articles', [HomeController::class, 'articles']);
    $r->addRoute('GET', '/login', [HomeController::class, 'login']);
    $r->addRoute('GET', '/register', [HomeController::class, 'register']);
    $r->post('/register', [HomeController::class, 'addUser']);
    $r->get('/article/add', [HomeController::class, 'addArticle']);
    $r->post('/article/add', [HomeController::class, 'addArticle']);
    $r->post('/login', [HomeController::class, 'loginUser']);
    $r->get('/logout', [HomeController::class, 'logout']);
    $r->get('/article/{id:\d+}', [HomeController::class, 'getArticle']);
});

$httpMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_SPECIAL_CHARS);
$uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_SPECIAL_CHARS);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

$handler = $routeInfo[1];
$vars = $routeInfo[2];

$controller = $handler[0];
$method = $handler[1];

$controllerObject = new $controller($bdd, $mail);
$controllerObject->$method($vars);
