<?php

namespace Zitro\Blog\Controllers;

use BDD;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class HomeController
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
    public function index(): void
    {
        $loader = new FilesystemLoader('../src/templates');
        $twig = new Environment($loader);

        // Chargement du template
        $template = $twig->load('pages/accueil.twig');

        // Affichage du template
        echo $template->render([
            'titre' => 'Mon Blog | Accueil',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function articles(): void
    {
        $loader = new FilesystemLoader('../src/templates');
        $twig = new Environment($loader, [
            'cache' => false,
            'strict_variables' => false,
            'debug' => true,
        ]);

        $twig->addExtension(new \Twig\Extension\DebugExtension());

        $result = $this->bdd->query("SELECT * FROM article");
        $articles = $result->fetch_all(MYSQLI_ASSOC);

        // Chargement du template
        $template = $twig->load('pages/articles.twig');

        // Affichage du template
        echo $template->render([
            'titre' => 'Mon Blog | Articles',
            'article' => $articles
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function login(): void
    {
        $loader = new FilesystemLoader('../src/templates');
        $twig = new Environment($loader);

        // Chargement du template
        $template = $twig->load('pages/login.twig');

        // Affichage du template
        echo $template->render([
            'titre' => 'Mon Blog | Connexion',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function register(): void
    {
        $loader = new FilesystemLoader('../src/templates');
        $twig = new Environment($loader);

        // Chargement du template
        $template = $twig->load('pages/register.twig');

        // Affichage du template
        echo $template->render([
            'titre' => 'Mon Blog | Inscription',
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function test(): void
    {
        $loader = new FilesystemLoader('../src/templates');
        $twig = new Environment($loader);

        // Chargement du template
        $template = $twig->load('pages/test.html.twig');

        // Affichage du template
        echo $template->render([
            'titre' => 'Mon Blog | Inscription',
        ]);
    }
}