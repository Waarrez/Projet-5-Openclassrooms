<?php

namespace Zitro\Blog\Controllers;

use BDD;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class AdminController
{
    private $bdd;

    public function __construct(BDD $bdd) {
        $this->bdd = $bdd;
    }

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

        if (isset($_SESSION["user_id"])) {
            $idUser = $_SESSION["user_id"];

            $request = $this->bdd->query("SELECT * FROM user WHERE id = '$idUser'");

            $user = $request->fetch_assoc();
        }

        // Affichage du template
        echo $template->render([
            'titre' => 'Mon Blog | Admin',
            'users' => $user
        ]);
    }
}