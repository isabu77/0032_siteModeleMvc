<?php

namespace App\Controller;

use \Core\Controller\Controller;
use \Core\Controller\Helpers\MailController;
use \Core\Controller\Helpers\TextController;
use \Core\Controller\FormController;
use \App\Model\Entity\UserEntity;

/**
 * Classe UsersController : Gestion des utilisateurs
 */
class UsersController extends Controller
{
    /**
     * constructeur
     */
    public function __construct()
    {
        // crée une instance de la classe UserTable dans la propriété
        // $this->users qui est créée dynamiquement
        $this->loadModel('user');
        $this->loadModel('userInfos');
    }

    /**
     * la connexion de l'utilisateur au site
     * @param UserEntity $user l'utilisateur
     *
     */
    private function connectSession(UserEntity $user)
    {
        $this->getApp()->getSession()->set("auth", $user);
    }

    /**
     * la déconnexion du site
     *
     */
    public function deconnectSession()
    {
        $this->getApp()->getSession()->delete("auth");
        header('Location: ' . $this->getUri("home"));
        exit();
    }

    /**
     * reset du password par mail
     * @param ?array $post tableau $_POST passé par le router
     *
     */
    public function resetpwd(?array $post = null)
    {
        if (isset($post["mail"]) && !empty($post["mail"])) {
            // vérifier l'existence du user en base
            $user = $this->user->getUserByMail($post["mail"]);
            if ($user) {
                // générer un nouveau mot de passe à sauvegarder dans la table users
                $passwordrdn = rand();
                $password = password_hash($passwordrdn, PASSWORD_BCRYPT);

                // modification des infos du user dans la base
                $res = $this->user->update($user->getId(), ["password" => $password]);
                if ($res) {
                    // envoyer nouveau mot de passe
                    $res = MailController::sendMail(
                        $post["mail"],
                        "Réinitialisation mdp",
                        "Le nouveau mot de passe est : " .  $passwordrdn
                    );
                    if ($res) {
                        $this->getFlashService()->addSuccess("Votre nouveau mot de passe vous a été envoyé par mail");
                        // Page de connexion
                        header('Location: ' . $this->getUri("userLogin"));
                        exit();
                    } else {
                        $this->getFlashService()->addAlert("Erreur d'envoi du mail, recommencez.");
                    }
                } else {
                    $this->getFlashService()->addAlert("Erreur de modification du mot de passe en base");
                }
            } else {
                $this->getFlashService()->addAlert("Cet utilisateur n'existe pas. Recommencez.");
            }
        }
        $title = 'Réinitialisation du mot de passe';

        // afficher la vue
        return $this->render('users/resetpwd', [
            'user' => $post,
            'title' => $title
        ]);
    }

    /**
     * la page d'inscription
     * @param ?array $post tableau $_POST passé par le router
     * @param ?int $idUser utilisateur
     * @param ?string $token token utilisateur
     *
     */
    public function inscription(?array $post = null, int $idUser = 0, string $token = "")
    {
        if (!empty($post)) {
            // traitement du formulaire
            $form = new FormController();
            $errors = $form->hasErrors();
            if (!isset($errors['post']) ||  $errors["post"] != "no-data") {
                $form->field('mail', ["require", "verify"]);
                $form->field('password', ["require", "verify"]);
                $form->field('lastname', ["require"]);
                $form->field('firstname', ["require"]);
                $form->field('latitude', ["require"]);
                $form->field('name', ["require"]);
                $form->field('longitude', ["require"]);
                $form->field('address', ["require"]);
                $form->field('zipCode', ["require"]);
                $form->field('city', ["require"]);
                $form->field('country', ["require"]);
                $form->field('phone', ["require"]);
                $errors = $form->hasErrors();
                if (empty($errors) && filter_var($post["mail"], FILTER_VALIDATE_EMAIL)) {
                    $datas = $form->getDatas();

                    // vérifier l'existence du user en base
                    $user = $this->user->getUserByMail($datas['mail']);

                    if (!$user) {
                        // il n'existe pas : insertion en base
                        $password = password_hash(htmlspecialchars($datas["password"]), PASSWORD_BCRYPT);
                        $token = TextController::randpwd(24);

                        // insérer l'objet en base dans la table users
                        $attributes =
                            [
                                "mail"         => htmlspecialchars($datas['mail']),
                                "password"     => $password,
                                "token"        => $token,
                                "verify"       => 0
                            ];

                        if ($this->user->insert($attributes)) {
                            $userId = $this->user->last();

                            // insérer l'adresse en base dans la table UserInfos
                            $attributes =
                                [
                                    "user_id"      => $userId,
                                    "lastname"     => htmlspecialchars($datas['lastname']),
                                    "firstname"    => htmlspecialchars($datas['firstname']),
                                    "address"      => htmlspecialchars($datas['address']),
                                    "zip_code"      => htmlspecialchars($datas['zipCode']),
                                    "city"         => htmlspecialchars($datas['city']),
                                    "country"      => htmlspecialchars($datas['country']),
                                    "phone"        => htmlspecialchars($datas['phone'])
                                ];
                            $this->userInfos->insert($attributes);

                            $user = $this->user->find($userId);
                            $uri = $this->getUri("inscription_Verify", ['id' => $userId, 'token' => $user->getToken()]);
                            // envoyer le mail de confirmation
                            $texte = ["html" => '<h1>Bienvenue sur notre site modèle'
                                . '</h1><p>Pour activer votre compte, veuillez cliquer sur le lien ci-dessous'
                                . ' ou copier/coller dans votre navigateur internet:</p><br />'
                                . '<a href="' . $uri
                                . '">Cliquez ICI pour valider votre compte</a><hr><p>Ceci est un mail automatique,'
                                . ' Merci de ne pas y répondre.</p>'];

                            $res = MailController::sendMail(
                                $datas["mail"],
                                "Confirmation Inscription au site modèle",
                                $texte
                            );
                            if ($res) {
                                $this->getFlashService()->addSuccess(
                                    "Veuillez confirmer votre inscription "
                                        . "en cliquant sur le lien qui vous a été envoyé par mail"
                                );
                            } else {
                                $this->getFlashService()
                                    ->addAlert("Erreur d'envoi du mail de confirmation, recommencez.");
                            }
                        } else {
                            //signaler erreur
                            $this->getFlashService()->addAlert("Erreur d'enregistrement en base, recommencez.");
                            header('Location: ' . $this->getUri("userSubscribe"));
                            exit();
                        }
                    } else {
                        // connecter l'utilisateur
                        // Page de connexion
                        header('Location: ' . $this->getUri("userLogin"));
                        exit();
                    }
                } else {
                    if (!filter_var($post["mail"], FILTER_VALIDATE_EMAIL)) {
                        $this->getFlashService()->addAlert("L'adresse mail est invalide.");
                    }
                    if ($post["mail"] !== $post["mailVerify"]) {
                        $this->getFlashService()->addAlert("Les deux mails ne correspondent pas.");
                    }
                    if ($post["password"] !== $post["passwordVerify"]) {
                        $this->getFlashService()->addAlert("Les deux mots de passe ne correspondent pas.");
                    }
                }
            }
        } else {
            // confirmation d'inscription par le mail envoyé
            if (
                isset($idUser) && !empty($idUser) &&
                isset($token) && !empty($token)
            ) {
                $user = $this->user->find($idUser);

                if ($user) {
                    if ($user->getToken() == $token) {
                        // validation en base
                        $res = $this->user->update($user->getId(), ["verify" => 1]);
                        if ($res) {
                            $this->getFlashService()
                                ->addSuccess('Votre inscription est validée, vous pouvez vous connecter.');
                            // Page de connexion
                            header('Location: ' . $this->getUri("userLogin"));
                            exit();
                        } else {
                            $this->getFlashService()
                                ->addAlert("Votre inscription n'est pas validée, veuillez recommencer.");
                        }
                    }
                } else {
                    $this->getFlashService()
                        ->addAlert("Cet utilisateur n'existe pas, veuillez recommencer votre inscription.");
                }
            }
        }

        $title = 'Inscription';

        // afficher la vue
        return $this->render('users/inscription', [
            'user' => $post,
            'title' => $title
        ]);
    }

    /**
     * Page Connexion
     * @param ?array $post tableau $_POST passé par le router
     */
    public function connexion(?array $post = null)
    {
        $datas = [];
        $bGoogle = false;
        $auth_url = "";

        if (!empty(\App\App::getInstance()->getEnv('GOOGLE_CLIENTID'))) {
            // pour connexion Google :
            // 1 : instancier un objet Google_Client
            // avec les paramètres trouvés dans la "Developers Console".
            $g_client = new \Google_Client();
            if ($g_client) {
                $g_client->setApplicationName("Mon application");
                // ne pas oublier setAccessType('offline')
                // sinon Google ne fournira pas de "refresh token" lors de l'autorisation et il faudra demander
                // à nouveau à l'utilisateur d'autoriser notre script quand l'access token expirera.
                $g_client->setAccessType('offline');
                $g_client->setClientId(\App\App::getInstance()->getEnv('GOOGLE_CLIENTID'));
                $g_client->setClientSecret(\App\App::getInstance()->getEnv('GOOGLE_SECRET'));
                $g_client->setRedirectUri($this->getUri(("userLogin")));
                $g_client->setScopes("email");
                // retourne une URL qui permet à l'utilisateur d'autoriser notre script.
                $auth_url = $g_client->createAuthUrl();
            }
        }

        // retour de la connexion Google :
        // on stocke le token dans $_SESSION["auth"]
        if (isset($_GET['code']) && !empty($_GET['code'])) {
            $code = $_GET['code'];
            try {
                $token = $g_client->fetchAccessTokenWithAuthCode($code);
                // assigne le token à l'objet Google_Client
                $g_client->setAccessToken($token);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            try {
                // récupérer l'email de connexion par google
                $datas['mail'] = $g_client->verifyIdToken()["email"];
                $bGoogle = true;
                // vérifier l'existence du mail en base sans son pwd
                $user = $this->user->getUserByMail($datas['mail']);
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        } elseif (!empty($post)) {
            // traitement du formulaire de connexion directe
            $form = new FormController();
            $errors = $form->hasErrors();
            if (!isset($errors['post']) ||  $errors["post"] != "no-data") {
                $form->field('mail', ["require"])
                    ->field('password', ["require"]);
                $errors = $form->hasErrors();
                if (empty($errors)) {
                    $datas = $form->getDatas();
                    // vérifier l'existence du mail en base avec son pwd
                    $user = $this->user->getUser($datas['mail'], $datas['password']);
                    // vérifier la confirmation par mail
                    if (!$user->getVerify()){
                        //signaler erreur
                        $this->getFlashService()->addAlert("Cette adresse mail n'a pas été confirmée, veuillez recommencer. ");
                        header('Location: ' . $this->getUri("userLogin"));
                        exit();
                    }
                } else {
                    //signaler erreur
                    $this->getFlashService()->addAlert($errors[0]);
                    header('Location: ' . $this->getUri("userLogin"));
                    exit();
                }
            } else {
                // formulaire vide
                //signaler erreur
                $this->getFlashService()->addAlert("Veuillez recommencer.");
                header('Location: ' . $this->getUri("userLogin"));
                exit();
            }
        }

        if (!empty($datas)) {
            if ($user) {
                // connecter l'utilisateur
                $user->setPassword("");
                $this->connectSession($user);
                if ($user->getToken() === "ADMIN") {
                    header('Location: ' . $this->getUri("admin"));
                } else {
                    header('Location: ' . $this->getUri("home"));
                }
                exit();
            } else {
                // supprimer la session auth
                $this->getApp()->getSession()->delete("auth");

                if ($bGoogle) {
                    // inscription de l'utilisateur connecté par google
                    // il n'existe pas : insertion en base
                    // avec un mot de passe et un token aléatoire
                    $datas['password'] = rand();
                    $password = password_hash(htmlspecialchars($datas["password"]), PASSWORD_BCRYPT);
                    $token = TextController::randpwd(24);

                    // insérer l'objet en base dans la table users
                    $attributes =
                        [
                            "mail"         => htmlspecialchars($datas['mail']),
                            "password"     => $password,
                            "token"        => $token,
                            "verify"       => 0
                        ];

                    if ($this->user->insert($attributes)) {
                        $userId = $this->user->last();
                        $user = $this->user->find($userId);

                        $attributes =
                            [
                                "user_id"      => $userId,
                                "lastname"     => "Google",
                                "firstname"    => "user",
                                "address"      => "adresse",
                                "zip_code"      => "00000",
                                "city"         => "Google",
                                "country"      => "Google",
                                "phone"        => "00000000"
                            ];
                        $this->userInfos->insert($attributes);

                        // lui envoyer un mail de confirmation et son mot de passe
                        $uri = $this->getUri("inscription_Verify", ['id' => $userId, 'token' => $user->getToken()]);
                        // envoyer le mail de confirmation
                        $texte = ["html" => '<h1>Bienvenue sur notre site modèle</h1>'
                            . '<p>Votre mot de passe sur ce site est ' . $datas["password"] . '</p><br />'
                            . '</p><p>Pour activer votre compte, veuillez cliquer sur le lien ci-dessous'
                            . ' ou copier/coller dans votre navigateur internet:</p><br />'
                            . '<p><a href="' . $uri
                            . '">Cliquez ICI pour valider votre compte</a><hr><p>Ceci est un mail automatique,'
                            . ' Merci de ne pas y répondre.</p>'];

                        $res = MailController::sendMail(
                            $datas["mail"],
                            "Confirmation Inscription au site modèle",
                            $texte
                        );
                        if ($res) {
                            $this->getFlashService()->addSuccess(
                                "Veuillez confirmer votre inscription "
                                    . "en cliquant sur le lien qui vous a été envoyé par mail"
                            );
                        } else {
                            $this->getFlashService()
                                ->addAlert("Erreur d'envoi du mail de confirmation, recommencez.");
                        }

                        // Page de connexion
                        header('Location: ' . $this->getUri("userLogin"));
                        exit();
                    } else {
                        //signaler erreur
                        $this->getFlashService()->addAlert("Erreur d'enregistrement en base, recommencez.");
                        header('Location: ' . $this->getUri("userLogin"));
                        exit();
                    }
                } else {
                    //signaler erreur
                    if ($user && !$user->getVerify()) {
                        $this->getFlashService()
                            ->addAlert("Votre inscription n'est pas validée, veuillez recommencer.");
                    } else {
                        $this->getFlashService()->addAlert("Adresse mail ou mot de passe invalide");

                        // avec FlashController dans $_SESSION['flash']
                        //$this->messageFlash()->error("Adresse mail ou mot de passe invalide");
                    }
                }
            }
        }

        // Page de connexion
        $title = 'Connexion';

        // afficher la vue
        return $this->render('users/connexion', [
            'title' => $title,
            "auth" => $auth_url
        ]);
    }

    /**
     * Page Contact
     * @param ?array $post tableau $_POST passé par le router
     */
    public function contact(?array $post = null)
    {
        if (!empty($post)) {
            if (
                isset($post["from"]) &&
                isset($post["object"]) &&
                isset($post["message"])
            ) {
                define('MAIL_TO', $this->getApp()->getEnv('GMAIL_USER'));
                define('MAIL_FROM', ''); // valeur par défaut
                define('MAIL_OBJECT', 'objet du message'); // valeur par défaut
                define('MAIL_MESSAGE', 'votre message'); // valeur par défaut
                // drapeau qui aiguille l'affichage du formulaire OU du récapitulatif
                $mailSent = false;
                // tableau des erreurs de saisie
                $errors = [];
                // si le courriel fourni est vide OU égale à la valeur par défaut
                $from = filter_input(INPUT_POST, 'from', FILTER_VALIDATE_EMAIL);
                if ($from === null || $from === MAIL_FROM) {
                    $errors[] = 'Vous devez renseigner votre adresse de courrier électronique.';
                    $this->getFlashService()->addAlert('Vous devez renseigner votre adresse de courrier électronique.');
                } elseif ($from === false) { // si le courriel fourni n'est pas valide
                    $errors[] = 'L\'adresse de courrier électronique n\'est pas valide.';
                    $from = filter_input(INPUT_POST, 'from', FILTER_SANITIZE_EMAIL);
                }
                $object = filter_input(
                    INPUT_POST,
                    'object',
                    FILTER_SANITIZE_STRING,
                    FILTER_FLAG_ENCODE_HIGH | FILTER_FLAG_ENCODE_LOW
                );
                // si l'objet fourni est vide, invalide ou égale à la valeur par défaut
                if ($object === null or $object === false or empty($object) or $object === MAIL_OBJECT) {
                    $errors[] = 'Vous devez renseigner l\'objet.';
                }
                $message = filter_input(INPUT_POST, 'message', FILTER_UNSAFE_RAW);
                // si le message fourni est vide ou égal à la valeur par défaut
                if ($message === null or $message === false or empty($message) or $message === MAIL_MESSAGE) {
                    $errors[] = 'Vous devez écrire un message.';
                }
                if (count($errors) === 0) { // si il n'y a pas d'erreur
                    // tentative d'envoi du message
                    if (MailController::sendMail(MAIL_TO, $object, $message, false, $from)) {
                        //if( mail( MAIL_TO, $object, $message, "From: $from\nReply-to: $from\n" ) )

                        $mailSent = true;
                    } else // échec de l'envoi
                    {
                        $errors[] = 'Votre message n\'a pas été envoyé.';
                    }
                }
                // si le message a bien été envoyé, on affiche le récapitulatif
                if ($mailSent === true) {
                    $this->getFlashService()->addSuccess('Votre message a bien été envoyé. Courriel pour la réponse :'
                        . $from . '. Objet : ' . $object . '. Message : ' . nl2br(htmlspecialchars($message)));
                } else {
                    // le formulaire est affiché pour la première fois
                    // ou le formulaire a été soumis mais contenait des erreurs
                    if (count($errors) !== 0) {
                        foreach ($errors as $key => $value) {
                            $this->getFlashService()->addAlert($value);
                        }
                    } else {
                        $this->getFlashService()->addAlert("Tous les champs sont obligatoires...");
                    }
                }
            }
        }

        $title = 'Contact';

        // afficher la vue
        return $this->render('users/contact', [
            'title' => $title
        ]);
    }
}
