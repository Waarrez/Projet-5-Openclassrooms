<?php

namespace Zitro\Blog\Controllers;

use BDD;
use JetBrains\PhpStorm\NoReturn;
use Mail;
use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Loader\FilesystemLoader;
use function RectorPrefix202307\dump;
use function RectorPrefix202307\Symfony\Component\DependencyInjection\Loader\Configurator\expr;

class HomeController
{

    private $bdd;
    private $mail;

    public function __construct(BDD $bdd, Mail $mail) {
        $this->bdd = $bdd;
        $this->mail = $mail;
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function index(): void
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
        $template = $twig->load('pages/accueil.twig');

        if (isset($_SESSION["user_id"])) {
            $idUser = $_SESSION["user_id"];

            $request = $this->bdd->query("SELECT * FROM user WHERE id = '$idUser'");

            $user = $request->fetch_assoc();
        }

        // Affichage du template
        echo $template->render([
            'titre' => 'Mon Blog | Accueil',
            'users' => $user
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

        $user = null;

        if (isset($_SESSION["user_id"])) {
            $idUser = $_SESSION["user_id"];

            $request = $this->bdd->query("SELECT * FROM user WHERE id = '$idUser'");

            $user = $request->fetch_assoc();
        }

        $twig->addExtension(new \Twig\Extension\DebugExtension());

        $result = $this->bdd->query("SELECT * FROM article");
        $articles = $result->fetch_all(MYSQLI_ASSOC);

        // Chargement du template
        $template = $twig->load('pages/articles.twig');

        // Affichage du template
        echo $template->render([
            'titre' => 'Mon Blog | Articles',
            'article' => $articles,
            'users' => $user
        ]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function getArticle($id): void
    {
        $loader = new FilesystemLoader('../src/templates');
        $twig = new Environment($loader, [
            'cache' => false,
            'strict_variables' => false,
            'debug' => true,
        ]);

        $article = null;

        foreach ($id as $identifiant) {
            $result = $this->bdd->query("SELECT * FROM article WHERE id = $identifiant");
            $article = $result->fetch_assoc();
        }

        $twig->addExtension(new \Twig\Extension\DebugExtension());


        $template = $twig->load('pages/article.twig');

        echo $template->render([
            'titre' => 'Mon Blog | Articles',
            'article' => $article
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

        // Définir la variable "titre" utilisée dans le fichier "base.html.twig"
        $titre = "Page d'inscription";

        // Afficher le template rendu avec la variable "titre"
        echo $template->render(['titre' => $titre]);
    }

    /**
     * @throws Exception
     */
    public function addUser(): void
    {
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            $email = $_POST["email"];
            $username = $_POST["username"];
            $password = $_POST["password"];
            $confirmPassword = $_POST["confirmPassword"];
            $roles = "ROLE_USER";
            $confirmAccount  = bin2hex(random_bytes(32));

            $hashPassword = password_hash($password, PASSWORD_ARGON2I);

            if(!empty($email) && !empty($username) && !empty($password) && !empty($confirmPassword)) {
                $verifyEmail = $this->bdd->query("SELECT * FROM user WHERE email = '$email'");

               if($verifyEmail->fetch_all() !== []) {
                   echo "Email deja utilisé";
               } else {
                   if($password !== $confirmPassword) {
                       echo "Vos mots de passes doivent correspondre";
                   } else {
                       $result = $this->bdd->query("INSERT INTO user (email,username,password, confirmAccount , roles) VALUES ('$email', '$username', '$hashPassword', '$confirmAccount' ,'$roles')");

                       if($result === TRUE) {
                            var_dump($this->mail->sendMail($email, $username, $confirmAccount));
                       } else {
                           echo "Erreur lors de l'inscription";
                       }
                   }
               }
            }
        }

        $loader = new FilesystemLoader('../src/templates');
        $twig = new Environment($loader);

        // Chargement du template
        $template = $twig->load('pages/register.twig');

        // Définir la variable "titre" utilisée dans le fichier "base.html.twig"
        $titre = "Page d'inscription";

        // Afficher le template rendu avec la variable "titre"
        echo $template->render(['titre' => $titre]);
    }

    public function addArticle(): void
    {
        $loader = new FilesystemLoader('../src/templates');
        $twig = new Environment($loader);

        // Chargement du template
        $template = $twig->load('pages/add_article.twig');

        // Définir la variable "titre" utilisée dans le fichier "base.html.twig"
        $titre = "Ajouter un article";



        if([$_SERVER["REQUEST_METHOD"] === "POST"]) {
            if (isset($_POST["title"]) && isset($_POST["content"])) {
                $title = $_POST["title"];
                $content = $_POST["content"];
                $publishedAt = new \DateTimeImmutable();
                $format = $publishedAt->format('Y-m-d');
                session_start();
                $authorId = $_SESSION["user_id"];

                $this->bdd->query("INSERT INTO article (title, content, publishedAt, author_id) VALUES ('$title', '$content', '$format', '$authorId')");
                header("Location: /"); // Correction ici
                exit(); // Assurez-vous de quitter le script après la redirection
                // Maintenant vous pouvez utiliser $title et $content en toute sécurité
            }
        }

        // Afficher le template rendu avec la variable "titre"
        echo $template->render(['titre' => $titre]);
    }


    public function loginUser(): void
    {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST["email"];
            $password = $_POST["password"];

            $stmt = $this->bdd->query("SELECT * FROM user WHERE email = '$email'");

            if ($stmt->num_rows === 1) {
                $row = $stmt->fetch_assoc();
                $userId = $row["id"];
                $hashPasswordFromDatabase = $row["password"];

                // Vérifier le mot de passe
                if (password_verify($password, $hashPasswordFromDatabase)) {
                    if($row["confirmAccount"] !== null) {
                        session_start();
                        $_SESSION["user_id"] = $userId; // Stocker l'ID de l'utilisateur dans la session
                        header("Location: /"); // Rediriger vers la page du tableau de bord après la connexion
                    } else {
                        echo "Veuillez confirmer votre compte, un email à été envoyé";
                    }
                } else {
                    // Mot de passe incorrect
                    echo "Mot de passe incorrect.";
                }
            } else {
                // L'utilisateur n'existe pas
                echo "Utilisateur non trouvé.";
            }

            $stmt->close();
        }
    }

    public function logout(): void
    {
        session_start();

        session_destroy();

        header("Location: /");
    }
}