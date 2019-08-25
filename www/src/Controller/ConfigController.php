<?php
namespace App\Controller;

use \Core\Controller\Controller;

/**
 * Classe ConfigController : accès à la table config
 */
class ConfigController extends Controller
{
    /**
     * constructeur
     */
    public function __construct()
    {
        // crée une instance de la classe ConfigTable dans la propriété $this->config
        // $this->config est créée dynamiquement
        $this->loadModel('config');
    }
}
