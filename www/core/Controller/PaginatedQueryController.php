<?php
namespace Core\Controller;

use \Core\Model\Table;
use \Core\Controller\URLController;

/**
 * classe PaginatedQueryController : gestion d'une pagination avec requête à la base
 *
 */
class PaginatedQueryController
{
    /**
     * @var string
     * @access private
     */
    protected $classTable;

    /**
     * @var string
     * @access private
     */
    protected $url;

    /**
     * @var int
     * @access private
     */
    protected $perPage;

    /**
     * @var array
     * @access private
     */
    protected $items;

    /**
     * @var int
     * @access private
     */
    protected $count;

    /**
     * constructeur
     * @param Table $classTable : classe de la Table
     * @param string $url : url de la page à afficher quand on change de page
     * @param int $perPage : nb d'items par page
     */
    public function __construct(
        Table $classTable,
        string $url = null,
        int $perPage = 7
    ) {
        $this->classTable = $classTable;
        $this->url = $url;
        $this->perPage = $perPage;
    }

    /**
     * retourne la page courante
     */
    protected function getCurrentPage(): int
    {
        return URLController::getPositiveInt('page', 1);
    }

    /**
     * retourne le nb de pages
     */
    private function getNbPages(int $id = null): float
    {
        if ($this->count === null) {
            $this->count = $this->classTable->count($id)->nbrow;
        }

        if ($this->count === "0" || $this->count === 0.0 || $this->count === 0) {
            $this->count = 0.1;
        }
        return ceil($this->count / $this->perPage);
    }


    /**
     * retourne le tableau des n° de page
     */
    public function getNav(): array
    {
        $uri = $this->url;
        $nbPage = $this->getNbPages();
        $navArray = [];
        for ($i = 1; $i <= $nbPage; $i++) {
            $url = $i == 1 ? $uri : $uri . "?page=" . $i;
            $navArray[$i] = $url;
        }
        return $navArray;
    }

    /**
     * Retourne la liste des éléments d'une page
     * par appel de la méthode de requête de la classe fournie
     * @param void
     * @return array
     */
    public function getItems(): ?array
    {
        $currentPage = $this->getCurrentPage();
        $nbPage = $this->getNbPages();

        if ($currentPage > $nbPage) {
            // page inexistante : page 1
            //throw new \Exception("pas de page");
            header('location: ' . $this->url);
            exit();
        }

        if ($this->items === null) {
            $offset = ($currentPage - 1) * $this->perPage;
            // lecture des éléments de la page dans la base
            $this->items = $this->classTable->allByLimit($this->perPage, $offset);
        }
        return ($this->items);
    }
    
    /**
     * Retourne la liste des éléments d'une page
     * par appel de la méthode de requête de la classe fournie
     * @param void
     * @return array
     */
    public function getItemsInId(int $id): ?array
    {
        $currentPage = $this->getCurrentPage();
        $nbPage = $this->getNbPages($id);

        if ($currentPage > $nbPage) {
            // page inexistante : page 1
            //throw new \Exception("pas de page");
            header('location: ' . $this->url);
            exit();
        }

        if ($this->items === null) {
            $offset = ($currentPage - 1) * $this->perPage;
            // lecture des éléments de la page dans la base
            $this->items = $this->classTable->allInIdByLimit($this->perPage, $offset, $id);
        }
        return ($this->items);
    }
}
