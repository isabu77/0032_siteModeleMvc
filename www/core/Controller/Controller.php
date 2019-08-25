<?php

namespace Core\Controller;

use Core\Controller\Session\FlashService;
use Core\Extension\Twig\FlashExtension;
use Core\Extension\Twig\PriceExtension;
use Core\Extension\Twig\UriExtension;

abstract class Controller
{
    /**
     * instance de la classe App (Application)
     */
    private $app;

    /**
     * instance du moteur de template Twig
     */
    private $twig;

    /**
     * instance de la classe FlashController(pour l'extension Twig)
     * NON UTILISé
     */
    private $messageFlash;

    /**
     * répertoire de base de l'appli
     */
    private $basePath;

    /**
     * répertoire contenant les images téléchargées
     */
    protected $uploadPath;
    
    protected $uploadPublic;

    /**
     * constructeur
     */
    public function __construct()
    {
        $this->basePath = dirname(dirname(__dir__)) . DIRECTORY_SEPARATOR;
        $this->uploadPath = $this->basePath . 'public/assets/upload/';
        $this->uploadPublic = '/assets/upload/';
        $this->loadModel('plant');
        $this->loadModel('group');
    }

    /**
     * Rendu d'une page en .twig
     */
    protected function render(string $view, array $variable = [])
    {
        $variable['debugTime'] = $this->getApp()->getDebugTime();
        $variable['cookie'] = $_COOKIE;
        $variable['session'] =  $this->getApp()->getSession()->getSessionGlobale();
        $variable['constant'] =  get_defined_constants();

        // remplacé par FlashExtension en toastr dans le twig de layout
        //$variable['success'] =  $this->getFlashService()->getMessages("success");
        //$variable['alert'] =  $this->getFlashService()->getMessages("alert");

        // charger l'extension dans Twig

        echo $this->getTwig()->render($view . '.twig', $variable);
    }

    /**
     * retourne l'instance du moteur de template Twig
     */
    private function getTwig()
    {
        if (is_null($this->twig)) {
            // initialisation de Twig : moteur de template PHP
            $loader = new \Twig\Loader\FilesystemLoader(dirname(dirname(__dir__)) . '/views/');
            $this->twig = new \Twig\Environment($loader);

            // ajouter les extensions Twig
            $this->twig->addExtension(new FlashExtension());
            $this->twig->addExtension(new PriceExtension());
            $this->twig->addExtension(new UriExtension());

            // passage des variables globales de session et de constantes
            //$this->twig->addGlobal('cookie', $_COOKIE);
            //$this->twig->addGlobal('session', $_SESSION);
            //$this->twig->addGlobal('constant', get_defined_constants());
        }

        return $this->twig;
    }

    /**
     * retourne l'instance de la classe App (Application)
     */
    protected function getApp()
    {
        if (is_null($this->app)) {
            $this->app = \App\App::getInstance();
        }
        return $this->app;
    }

    /**
     * retourne l'instance de la classe FlashController(pour l'extension Twig)
     */
    protected function messageFlash()
    {
        if (is_null($this->messageFlash)) {
            $this->messageFlash = new FlashController();
        }
        return $this->messageFlash;
    }

    /**
     * retourne l'instance de la classe FlashService
     */
    protected function getFlashService(): FlashService
    {
        return $this->getApp()->getFlashService();
    }

    /**
     * génère l'Url de la route pour la page routeName
     */
    protected function generateUrl(string $routeName, array $params = []): string
    {
        //return $this->getApp()->getRouter()->url($routeName, $params);
        return URLController::getUri($routeName, $params);
    }

    /**
     * génère une uri entière avec http:// .... à partir d'une route
     */
    protected function getUri(string $routeName, array $params = []): string
    {
        return URLController::getUri($routeName, $params);
    }

    /**
     * crée dynamiquement une instance de la classe $nameTable
     * et la stocke dans la propriété de nom $nameTable
     * héritée dans sa sous-classe qui appelle ce loadModel dans son constructeur
     */
    protected function loadModel(string $nameTable): void
    {
        // crée une propriété de nom '$nameTable' contenant l'instance de la sous-classe de Table
        // (par exemple : 'post' crée une instance $post= new PostTable() )
        $this->$nameTable = $this->getApp()->getTable($nameTable);
    }

    /**
     * téléchargement d'un fichier image à partir de l'input hFichier
     * @param ?string $fileName préfixe du fichier téléchargé, null si pas de fichier
     * @return string nom du fichier téléchargé dans public/assets/upload/' (dossier en chmod 777)
     */
    public function getImageFile(string $fileName): ?string
    {
        $max_size = 5242880; // Taille max en octets du fichier
        $tabExt = array('jpg', 'gif', 'png', 'jpeg'); // Extensions autorisees
        // On verifie si le champ est rempli
        if (!empty($_FILES['hFichier']['name'])) {
            // Recuperation de l'extension du fichier
            $extension  = pathinfo($_FILES['hFichier']['name'], PATHINFO_EXTENSION);
            // On verifie l'extension du fichier
            if (in_array(strtolower($extension), $tabExt)) {
                // On recupere les dimensions du fichier
                $infosImg = getimagesize($_FILES['hFichier']['tmp_name']);
                // On verifie le type de l'image
                if ($infosImg[2] >= 1 && $infosImg[2] <= 14) {
                    // On verifie la taille de l'image
                    if (filesize($_FILES['hFichier']['tmp_name']) <= $max_size) {
                        // Parcours du tableau d'erreurs
                        if (isset($_FILES['hFichier']['error']) && UPLOAD_ERR_OK === $_FILES['hFichier']['error']) {
                            // On renomme le fichier
                            $nomImage = $fileName . '_' . md5(uniqid()) . '.' . $extension;
                            // Si c'est OK, on teste l'upload
                            if (move_uploaded_file($_FILES['hFichier']['tmp_name'], $this->uploadPath . $nomImage)) {
                                chmod($this->uploadPath . $nomImage, 0777);
                                //$this->getFlashService()->addSuccess('upload ok');
                                return ($nomImage);
                            } else {
                                $this->getFlashService()->addAlert('sql error');
                            }
                        } else {
                            $this->getFlashService()->addAlert('error 500');
                        }
                    } else {
                        $this->getFlashService()->addAlert('no match size');
                    }
                } else {
                    $this->getFlashService()->addAlert('no match type');
                }
            } else {
                $this->getFlashService()->addAlert('no match ext');
            }
        } else {
            $this->getFlashService()->addAlert("empty input");
        }
        return ($nomImage);
    }
}
