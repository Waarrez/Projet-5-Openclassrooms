<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Zitro\Blog\Controllers\HomeController;

// Inclure la classe BDD si elle n'est pas déjà incluse
require_once '../src/Classes/BDD.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Informations de connexion à la base de données
$servername = "localhost:3307"; // Remplacez par l'hôte de votre base de données
$username = "root"; // Remplacez par votre nom d'utilisateur de la base de données
$password = ""; // Remplacez par votre mot de passe de la base de données
$dbname = "blog_php"; // Remplacez par le nom de votre base de données

// Créer une instance de la classe BDD pour la connexion
$bdd = new BDD($servername, $username, $password, $dbname);

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', [HomeController::class, 'index']);
    $r->addRoute('GET', '/articles', [HomeController::class, 'articles']);
    $r->addRoute('GET', '/login', [HomeController::class, 'login']);
    $r->addRoute('GET', '/register', [HomeController::class, 'register']);
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
