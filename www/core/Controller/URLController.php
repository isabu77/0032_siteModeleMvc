<?php

namespace Core\Controller;

/**
 * Classe statique de contrôle d'une Url
 */
class URLController
{

    /**
     * @param string
     * @param int
     * @return int
     */
    public static function getInt(string $name, ?int $default = null): ?int
    {
        if (!isset($_GET[$name])) {
            return $default;
        }

        if ($_GET[$name] === '0') {
            return 0;
        }

        if (!filter_var($_GET[$name], FILTER_VALIDATE_INT)) {
            throw new \Exception("Le paramètre '$name' n'est pas un entier");
        }
        return ((int) $_GET[$name]);
    }

    /**
     * @param string
     * @param int
     * @return int
     */
    public static function getPositiveInt(string $name, ?int $default = null): ?int
    {
        $param = self::getInt($name, $default);
        if ($param  !== null && $param <= 0) {
            throw new \Exception("Le paramètre '$name' n'est pas un entier positif");
        }
        return $param;
    }

    /**
     * génère une uri entière avec http:// .... à partir d'une route
     * existe aussi dans App.php de l'application
     * avec appel de $this->getRouter()->url($routeName, $params);
     *
     * @return string
     */
    public static function getUri(string $routeName, array $params = []): string
    {
        if (isset($_SERVER['REQUEST_SCHEME']) && !empty($_SERVER['REQUEST_SCHEME'])){
            // si http ou https
            $uri = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'];
        }else{
            // si serveur distant
            $uri = "";//$_SERVER['HTTP_HOST'];
        }

        $folder = \App\App::getInstance()->getRouter()->url($routeName, $params);

        return $uri . $folder;

        //global $racine; // définie dans config.php (false si wamp, true si serveur externe)
        //$folder = ""; // dossier courant
        //$uri = $_SERVER['HTTP_X_FORWARDED_PROTO'] . "://" . $_SERVER['HTTP_HOST'];
        //if (!$racine) {
        //    $folder = basename(dirname(dirname(__FILE__))) . '/';
        //}
        //return $uri . '/' . $folder . $cible;
    }
}
