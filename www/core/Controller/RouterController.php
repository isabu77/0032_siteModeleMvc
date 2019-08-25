<?php
namespace Core\Controller;

class RouterController
{
    private $router;
    private $viewPath;

    /**
     * constructeur
     */
    public function __construct(string $viewPath)
    {
        $this->viewPath = $viewPath;
        $this->router = new \AltoRouter();
    }

    /**
     * get
     */
    public function get(string $uri, string $file, string $name): self
    {
        $this->router->map('GET', $uri, $file, $name);
        return $this; // pour enchainer à l'appel
    }
    /**
     * post
     */
    public function post(string $uri, string $file, string $name): self
    {
        $this->router->map('POST', $uri, $file, $name);
        return $this; // pour enchainer à l'appel
    }

    /**
     * match : GET et POST sur le même nom de route
     */
    public function match(string $uri, string $file, string $name): self
    {
        $this->router->map('GET|POST', $uri, $file, $name);
        return $this; // pour enchainer à l'appel
    }

    /**
     * génère une url avec une route par Altorouter
     */
    public function url(string $name, array  $params = []): string
    {
        return $this->router->generate($name, $params);
    }

    /**
     * lance la route qui matche
     */
    public function run(): void
    {
        $match = $this->router->match();

        // var_dump("[== MATCH : ");
        // var_dump($match);
        // var_dump(" ==]");
        
        // on définit une variable avec l'instance pour appeler url()
        // dans toutes les vues qui sont incluses ci-dessous (dans le dossier views)
        $router = $this;
        if (is_array($match)) {
            if (strpos($match['target'], "#")) {
                [$controller, $methode] = explode("#", $match['target']);
                $controller = "App\\Controller\\" . ucfirst($controller) . "Controller";
                // traitement des formulaires : envoi du $_POST
                if (!empty($_POST)) {
                    (new $controller())->$methode($_POST, ...array_values($match['params']));
                } else {
                    // traitement d'une route avec params : on transforme le tableau en liste de paramètres
                    (new $controller())->$methode(null, ...array_values($match['params']));
                }
                exit();
            }
            $params = $match['params'];
            require $this->pathToFile($match['target']);
        } else {
            // no route was matched
            header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
            require $this->pathToFile("layout/404");
        }
    }

    /**
     *
     */
    private function pathToFile(string $file): string
    {
        return $this->viewPath . DIRECTORY_SEPARATOR . $file . '.php';
    }
}
