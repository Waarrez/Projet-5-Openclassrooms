<?php

namespace Zitro\Blog\Controllers;

use BDD;
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
        }

        $result = $this->bdd->query("SELECT * FROM article");
        $articles = $result->fetch_all(MYSQLI_ASSOC);

        // Affichage du template
        echo $template->render([
            'titre' => 'Mon Blog | Admin',
            'users' => $user,
            'articles' => $articles
        ]);
    }
}