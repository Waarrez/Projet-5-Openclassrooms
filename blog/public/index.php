<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Zitro\Blog\Controllers\HomeController;
use Zitro\Blog\Controllers\AdminController;

// Inclure la classe BDD si elle n'est pas déjà incluse
require_once '../src/Classes/BDD.php';

session_start();

$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "blog_php";

// Créer une instance de la classe BDD pour la connexion
$bdd = new BDD($servername, $username, $password, $dbname);

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('GET', '/articles', [HomeController::class, 'articles']);
    $r->addRoute('GET', '/login', [HomeController::class, 'login']);
    $r->addRoute('GET', '/register', [HomeController::class, 'register']);
    $r->addRoute('GET', '/confirmAccount', [HomeController::class, 'confirmAccount']);
    $r->addRoute('GET', '/admin', [AdminController::class, 'indexAdmin']);
    $r->get('/article/add', [HomeController::class, 'addArticle']);
    $r->get('/logout', [HomeController::class, 'logout']);
    $r->get('/confirmAccount/{test}', [HomeController::class, 'confirmAccount']);
    $r->get('/article/{id:\d+}', [HomeController::class, 'getArticle']);
    $r->get('/commentary/approuve/{id:\d+}',[HomeController::class, 'approuveCommentary']);
    $r->get('/admin/article/{id:\d+}',[AdminController::class, 'editAdminArticle']);
    $r->get('/article/edit/{id:\d+}', [HomeController::class, 'editArticle']);
    $r->get('/delete/article/{id:\d+}', [AdminController::class, 'deleteArticle']);
    $r->get('/delete/user/{id:\d+}', [AdminController::class, 'deleteUser']);
    $r->post('/article/{id:\d+}', [HomeController::class, 'getArticle']);
    $r->post('/article/edit/{id:\d+}', [HomeController::class, 'editArticle']);
    $r->post('/article/add', [HomeController::class, 'addArticle']);
    $r->post('/login', [HomeController::class, 'loginUser']);
    $r->post('/commentary/approuve/{id:\d+}',[HomeController::class, 'approuveCommentary']);
    $r->post('/register', [HomeController::class, 'addUser']);
    $r->post('/admin/article/{id:\d+}',[AdminController::class, 'editAdminArticle']);
    $r->post('/',[HomeController::class, 'index']);
});

$httpMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_SPECIAL_CHARS);
$uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_SPECIAL_CHARS);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

$handler = $routeInfo[1];
$vars = $routeInfo[2];

$controller = $handler[0];
$method = $handler[1];

$controllerObject = new $controller($bdd);
$controllerObject->$method($vars);
