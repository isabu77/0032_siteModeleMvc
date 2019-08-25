<?php

namespace App\Controller\Admin;

use \Core\Controller\Controller;
use \Core\Model\UserEntity;

/**
 * Classe AdminController : Administration du site
 */
class AdminController extends Controller
{
    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->loadModel('user');
    }

    /**
     * page d'accueil de l'administration du site
     */
    public function index()
    {
        // l'utilisateur connecté
        $userConnect = $this->getApp()->getConnectedUser();
        if ($userConnect->GetToken() != "ADMIN") {
            $this->getFlashService()->addAlert("Accès refusé. Veuillez vous connecter en mode Administrateur.");
            $this->getApp()->getSession()->delete("auth");
            // Page de connexion
            header('Location: ' . $this->getUri("userLogin"));
            exit();
        }

        // afficher la vue
        $title = "Administration";
        return $this->render("admin/index", [
                'admin' => true,
                "title" => $title
        ]);
    }
}
