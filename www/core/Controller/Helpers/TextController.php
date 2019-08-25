<?php
namespace Core\Controller\Helpers;

/**
 *  Classe Text
 * @var string
 * @access public
 * @static
 **/
class TextController
{
    /**
     *  extrait du contenu
     * @param string
     * @param int
     * @return string
     * @access public
     * @static
     **/
    public static function excerpt(string $content, int $limit = 100): string
    {

        // pour oter les balises html :
        //$content = strip_tags($content);
        // avec une expression régulière : remplacer tout ce qui est entre < et > par rien
        $text = preg_replace('/<(.*?)>/', "", $content);

        // si la chaine est plus petite que la limite, on la rend entière
        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        // la manière la plus factorisée :
        return mb_substr($text, 0, mb_strpos($text, ' ', $limit - 1) ?: $limit) . '...';

        // en décomposition :
        // pour ne pas couper le dernier mot, on cherche le premier espace derrière la limite
        // $limit-1 pour gérer le cas d'un espace en position 100
        //$lastSpace = mb_strpos($text, ' ', $limit-1);

        // cas d'une chaine sans espaces : on tronque à la limite demandée
        //$lastSpace = $lastSpace?: $limit;

        //$lastSpace = lastSpace?: $limit;

        // autres cas : on tronque à la limite ou après le dernier mot derrière la limite et ajout des '...'
        //return mb_substr($text, 0, $lastSpace) . '...';
    }

    public static function randpwd($nb_car = 10, $chaine = 'azertyuiopqsdfghjklmwxcvbn0123456789')
    {
        $nb_lettre = strlen($chaine) - 1;
        $generation = '';
        for ($i = 0; $i < $nb_car; $i++) {
            $pos = mt_rand(0, $nb_lettre);
            $car = $chaine[$pos];
            $generation .= $car;
        }
        return $generation;
    }

    /**
     * afficher un message FLASH
     * @return void
     */
    public static function displayFlashMessage($info = "", $succes = "", $erreur = "")
    {
        // php-flash-messages fonctionne avec bootstrap !!
        // Start a Session
        if (!session_id()) {
            @session_start();
        }

        // Instantiate the class
        $msg = new \Plasticbrain\FlashMessages\FlashMessages();
        // Add messages
        if (!empty($succes)) {
            $msg->success($succes, null, true);
        }
        if (!empty($erreur)) {
            $msg->error($erreur, null, true);
        }
        if (!empty($info)) {
            $msg->info($info, null, true);
        }

        // affichage
        $msg->display();
    }
}
