<?php

namespace Core\Extension\Twig;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * Classe d'extension de Twig pour calculer le prix ttc
 */
class PriceExtension extends AbstractExtension
{
    /**
     * retourne la méthode dans la vue par le mot 'flash'
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getPriceTtc', [$this, 'getPriceTtc']),
            new TwigFunction('getPriceHt', [$this, 'getPriceHt']),
            new TwigFunction('tourne', [$this, 'tourne'])
        ];
    }

    /**
     * prix ttc
     */
    public function getPriceTtc(float $priceHt): string
    {
        return number_format(($priceHt * TVA), 2, ',', ' ') . '€ TTC';
    }

    /**
     * prix ht
     */
    public function getPriceHt(float $priceHt): string
    {
        return number_format($priceHt, 2, ',', ' ') . '€ HT';
    }

    /**
     * affiche tous les chiffres inférieurs ou égaux à $num
     */
    public function tourne(int $num): string
    {
        $str = "-";
        for ($i = 1; $i <= $num; $i++) {
            $str .= number_format($i) . '-';
        }
        return $str ;
    }
}
