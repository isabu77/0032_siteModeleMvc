<?php

namespace Core\Extension\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;
use Core\Controller\URLController;

/**
 * Classe d'extension de Twig pour les uri
 */
class UriExtension extends AbstractExtension
{
    /**
     * retourne la méthode dans la vue par le mot 'uri'
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('uri', [$this, 'getUri'])
        ];
    }

    /**
     * retourne l'uri d'une route
     */
    public function getUri(string $routeName, array $params = []): string
    {
        return URLcontroller::getUri($routeName, $params);
    }
}
