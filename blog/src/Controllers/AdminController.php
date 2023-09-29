<?php

namespace Zitro\Blog\Controllers;

use BDD;
use DateTimeImmutable;
use Zitro\Blog\Classes\SessionManager;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class AdminController
{
    private $bdd;

    public function __construct(BDD $bdd) {
        $this->bdd = $bdd;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function indexAdmin(): void
    {
        $loader = new FilesystemLoader('../src/templates');
        $twig = new Environment($loader, [
            'cache' => false,
            'strict_variables' => false,
            'debug' => true,
        ]);

        $twig->addExtension(new \Twig\Extension\DebugExtension());

        $user = null;

        // Chargement du template
        $template = $twig->load('admin/admin.twig');

        $idUser = SessionManager::get("user_id");
        // Chargement du template
        $template = $twig->load('admin/admin.twig');

        if (isset($_SESSION["user_id"])) {

            $request = $this->bdd->query("SELECT * FROM user WHERE id = '$idUser'");

            $user = $request->fetch_assoc();

            if($user['roles'] !== "ROLE_ADMIN") {
                header('Location: /');
            }
        } else {
            header('Location: /');
        }

        $result = $this->bdd->query("SELECT * FROM article");
        $articles = $result->fetch_all(MYSQLI_ASSOC);

        $request = $this->bdd->query("SELECT * FROM user");
        $allUsers =  $request->fetch_all(MYSQLI_ASSOC);

        // Affichage du template
        echo $template->render([
            'titre' => 'Mon Blog | Admin',
            'users' => $user,
            'articles' => $articles,
            'allUsers' => $allUsers
        ]);
    }

    public function editAdminArticle($id): void
    {
        if(isset($_SESSION['user_id'])) {
            $users = null;

            $idUser = $_SESSION["user_id"];

            $request = $this->bdd->query("SELECT * FROM user WHERE id = '$idUser'");

            $users = $request->fetch_assoc();

            if($users['roles'] !== "ROLE_ADMIN") {
                header('Location: /');
            }

        } else {
            header('Location: /');
        }

        $loader = new FilesystemLoader('../src/templates');
        $twig = new Environment($loader, [
            'cache' => false,
            'strict_variables' => false,
            'debug' => true,
        ]);

        // Chargement du template
        $template = $twig->load('pages/modify_article.twig');

        $titre = "Modifier un article";

        $article = null;
        $idArticle = null;

        foreach ($id as $identifiant) {
            $result = $this->bdd->query("SELECT * FROM article WHERE id = $identifiant");
            $article = $result->fetch_assoc();
            $idArticle = $identifiant;
        }

        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $title = $_POST["title"];
            $content = $_POST["content"];
            $date = new DateTimeImmutable();
            $formatDate = $date->format('Y-m-d');

            $this->bdd->query("UPDATE article SET title = '$title', content = '$content', publishedAt = '$formatDate' WHERE id = $idArticle");
            header("Location: /admin");
            exit();
        }

        // Afficher le template rendu avec la variable "titre"
        echo $template->render([
            'titre' => $titre,
            'article' => $article,
            'users' => $users
        ]);
    }

    public function deleteArticle($id): void
    {
        if(isset($_SESSION['user_id'])) {
            $users = null;

            $idUser = $_SESSION["user_id"];

            $request = $this->bdd->query("SELECT * FROM user WHERE id = '$idUser'");

            $users = $request->fetch_assoc();

            if($users['roles'] !== "ROLE_ADMIN") {
                header('Location: /');
            }

        } else {
            header('Location: /');
        }

        foreach ($id as $article) {
            $this->bdd->query("DELETE FROM article WHERE id = '$article' ");
            header('Location: /admin');
        }
    }

    public function deleteUser($id): void
    {
        if(isset($_SESSION['user_id'])) {
            $users = null;

            $idUser = $_SESSION["user_id"];

            $request = $this->bdd->query("SELECT * FROM user WHERE id = '$idUser'");

            $users = $request->fetch_assoc();

            if($users['roles'] !== "ROLE_ADMIN") {
                header('Location: /');
            }

        } else {
            header('Location: /');
        }

        foreach ($id as $user) {
            $this->bdd->query("DELETE FROM user WHERE id = '$user' ");
            header('Location: /admin');
        }
    }
}