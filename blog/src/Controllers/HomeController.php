<?php

namespace Zitro\Blog\Controllers;

use BDD;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
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
    public function admin(): void
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


        // Affichage du template
        echo $template->render([
            'titre' => 'Admin | Accueil'
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
        $comments = null;
        $users = array();

        foreach ($id as $identifiant) {
            $result = $this->bdd->query("SELECT * FROM article WHERE id = $identifiant");
            $resultComment = $this->bdd->query("SELECT * FROM comment WHERE article_id = $identifiant");
            $article = $result->fetch_assoc();
            $comments = $resultComment->fetch_all();

            foreach ($comments as $comment) {
                $authorId = $comment[4];
                $user = $this->bdd->query("SELECT username FROM user WHERE id = $authorId ");
                $authorInfo = $user->fetch_assoc();

                if ($authorInfo) { // Vérifiez si des informations d'utilisateur ont été trouvées
                    $username = $authorInfo['username'];
                    $users[] = $username;
                }
            }
        }

        // Ajout d'un commentaire
        if($_SERVER["REQUEST_METHOD"] === "POST") {
            if($_POST["comment"] !== null) {
                $comment = $_POST["comment"];
                $postedAt = new \DateTimeImmutable();
                $format = $postedAt->format('Y-m-d');
                $user_id = $_SESSION["user_id"];
                $article_id = $article["id"];

                $result = $this->bdd->query("INSERT INTO comment (content, postedAt, article_id, author_id) VALUES ('$comment', '$format', '$article_id' ,'$user_id')");
                if($result === TRUE) {
                    header("Location: /article/$article_id");
                }
            }
        }

        $twig->addExtension(new \Twig\Extension\DebugExtension());


        $template = $twig->load('pages/article.twig');

        echo $template->render([
            'titre' => 'Mon Blog | Articles',
            'article' => $article,
            'comments' => $comments,
            'users' => $users
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
            $content = $_POST["content"];
            $file = $_FILES["file"];
            $filePdf = $_FILES["file_pdf"];
            $roles = "ROLE_USER";
            $confirmAccount  = bin2hex(random_bytes(32));

            $hashPassword = password_hash($password, PASSWORD_ARGON2I);

            if(!empty($email) && !empty($username) && !empty($password) && !empty($confirmPassword) && !empty($file)) {
                $verifyEmail = $this->bdd->query("SELECT * FROM user WHERE email = '$email'");

               if($verifyEmail->fetch_all() !== []) {
                   echo "Email deja utilisé";
               } else {
                   if($password !== $confirmPassword) {
                       echo "Vos mots de passes doivent correspondre";
                   } else {
                       // Vérifier s'il y a eu une erreur lors du téléchargement
                       if ($file['error'] === UPLOAD_ERR_OK) {
                           $uploadDir = '../public/uploads/'; // Répertoire où les images seront stockées
                           $uploadPath = $uploadDir . basename($file['name']);

                           $img = basename($file['name']);
                           // Déplacer le fichier téléchargé vers le répertoire spécifié
                           move_uploaded_file($file['tmp_name'], $uploadPath);
                       } else {
                           echo "Une erreur s'est produite lors du téléchargement de l'image.";
                       }

                       // Ajout de la partie pour le PDF
                       if ($filePdf['error'] === UPLOAD_ERR_OK) {
                           $uploadPdfDir = '../public/uploads/pdf/'; // Répertoire où les PDF seront stockés
                           $uploadPdfPath = $uploadPdfDir . basename($filePdf['name']);

                           $pdf = basename($filePdf['name']);
                           // Déplacer le fichier PDF téléchargé vers le répertoire spécifié
                           move_uploaded_file($filePdf['tmp_name'], $uploadPdfPath);
                       } else {
                           echo "Une erreur s'est produite lors du téléchargement du PDF.";
                       }

                       $result = $this->bdd->query("INSERT INTO user (email,username,password, confirmAccount , roles, file, pdf , content) VALUES ('$email', '$username', '$hashPassword', '$confirmAccount' ,'$roles', '$img', '$pdf','$content')");

                       if($result === TRUE) {
                           $mail = new PHPMailer();

                           try {
                               // Paramètres du serveur SMTP pour Gmail
                               $mail->isSMTP();
                               $mail->Host       = 'smtp.gmail.com';
                               $mail->SMTPAuth   = true;
                               $mail->Username   = 'thimote.cabotte6259@gmail.com'; // Votre adresse Gmail complète
                               $mail->Password   = 'qxdm rcxk xqwe vnjz'; // Mot de passe de votre compte Gmail
                               $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                               $mail->Port = 465;

                               // Destinataire
                               $mail->setFrom('thimote.cabotte6259@gmail.com@gmail.com', 'Blog PHP');
                               $mail->addAddress($email, $username);

                               // Contenu du message
                               $mail->isHTML(true);
                               $mail->Subject = 'Activation de votre compte';
                               $mail->Body = 'Veuillez activer votre compte grâce à ce lien : <a href="http://127.0.0.1:8002/confirmAccount/' . $confirmAccount . '">Cliquez ici</a>';
                               $mail->AltBody = 'Contenu du message en texte brut (pour les clients ne prenant pas en charge HTML)';

                               // Envoyer l'e-mail
                               // Envoyer l'e-mail
                               if ($mail->send()) {
                                   header("Location: /");
                               } else {
                                   echo 'Erreur lors de l\'envoi de l\'e-mail : ' . $mail->ErrorInfo;
                               }
                               echo 'L\'e-mail a été envoyé avec succès.';
                           } catch (Exception $e) {
                               echo "Une erreur s'est produite lors de l'envoi de l'e-mail : {$mail->ErrorInfo}";
                           }
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

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function addArticle(): void
    {
        $loader = new FilesystemLoader('../src/templates');
        $twig = new Environment($loader, [
            'cache' => false,
            'strict_variables' => false,
            'debug' => true,
        ]);

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
                header("Location: /articles");
                exit();
            }
        }

        $template = $twig->load('pages/add_article.twig');

        echo $template->render(['titre' => $titre]);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function modifyArticle($id): void
    {
        $loader = new FilesystemLoader('../src/templates');
        $twig = new Environment($loader, [
            'cache' => false,
            'strict_variables' => false,
            'debug' => true,
        ]);

        $twig->addExtension(new \Twig\Extension\DebugExtension());

        // Chargement du template
        $template = $twig->load('pages/modify_article.twig');

        // Définir la variable "titre" utilisée dans le fichier "base.html.twig"
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

            $this->bdd->query("UPDATE article SET title = '$title', content = '$content' WHERE id = $idArticle");
            header("Location: /articles"); // Correction ici
            exit(); // Assurez-vous de quitter le script après la redirection
            // Maintenant vous pouvez utiliser $title et $content en toute sécurité
        }

        // Afficher le template rendu avec la variable "titre"
        echo $template->render(['titre' => $titre, 'article' => $article]);
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
                    if($row["confirmAccount"] === null) {
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

    public function confirmAccount(array $verify): void
    {
        foreach ($verify as $token) {
            $request = $this->bdd->query("SELECT * FROM user WHERE confirmAccount = '$token'");
            $user = $request->fetch_assoc();
            $idUser = $user["id"];

            if($user) {
                $this->bdd->query("UPDATE user set confirmAccount = null WHERE id = '$idUser'");
                echo "Votre compte est confirmé, pour vous connecter cliquez ici <a href='/login'>Connexion</a>";
            } else {
                echo "Mauvais compte";
            }
        }
    }

    public function logout(): void
    {
        session_start();

        session_destroy();

        header("Location: /");
    }
}