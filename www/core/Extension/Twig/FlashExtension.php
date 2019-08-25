<?php

namespace Core\Extension\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Core\Controller\FlashController;
use Core\Controller\Session\FlashService;

/**
 * Classe d'extension de Twig pour les messages Flash
 */
class FlashExtension extends AbstractExtension
{

    /**
     * FlashService
     */
    private $flashService;

    public function __construct()
    {
        $this->flashService = \App\App::getInstance()->getFlashService();
        //$this->flashService = new FlashController();
    }

    /**
     * retourne la méthode dans la vue par le mot 'flash'
     */
    public function getFunctions(): array
    {
        return [
            //new TwigFunction('flash', [$this, 'getFlash'])
            new TwigFunction('flash', [$this, 'getMessages'])
        ];
    }

    /**
     * retourne les messages d'un type error ou success
     * de FlashContoller
     */
    //public function getFlash(string $type): ?array
    //{
        //return $this->flashService->get($type);
    //}
 
    /**
     * retourne les messages d'un type error ou success
     * de FlashService
     */
    public function getMessages(string $type): array
    {
        return $this->flashService->getMessages($type);
    }
}
