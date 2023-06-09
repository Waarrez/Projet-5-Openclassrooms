<?php

namespace Zitro\Blog\Controllers;

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;

class HomeController
{
    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index() {
        $loader = new FilesystemLoader('../src/templates');
        $twig = new Environment($loader);

        // Chargement du template
        $template = $twig->load('base.html.twig');

        // Affichage du template
        echo $template->render([
            'titre' => 'Mon Blog',
        ]);
    }
}