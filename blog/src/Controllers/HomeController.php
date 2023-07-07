<?php

namespace Zitro\Blog\Controllers;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Setup;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use Zitro\Blog\Entity\User;

class HomeController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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

        $metadata = $this->entityManager->getClassMetadata(User::class);

        dump($metadata);

        // Chargement du template
        $template = $twig->load('pages/accueil.html.twig');

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
        $twig = new Environment($loader);

        // Chargement du template
        $template = $twig->load('pages/articles.html.twig');

        // Affichage du template
        echo $template->render([
            'titre' => 'Mon Blog | Articles',
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
        $template = $twig->load('pages/login.html.twig');

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
        $template = $twig->load('pages/register.html.twig');

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