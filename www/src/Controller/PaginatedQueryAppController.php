<?php

namespace App\Controller;

use \Core\Controller\PaginatedQueryController;

/**
 * classe PaginatedQueryAppController : gestion d'une pagination avec requête à la base
 *
 */
class PaginatedQueryAppController extends PaginatedQueryController
{
    /**
     * pagination html général
     */
    public function getNavHtml(): string
    {
        $urls = $this->getNav();
        $html = "";
        foreach ($urls as $key => $url) {
            $class = $this->getCurrentPage() == $key ? " active" : "";
            $html .= "<li class=\"page-item {$class}\"><a class=\"page-link\" href=\"{$url}\">{$key}</a></li>";
        }
        return <<<HTML
    <nav class="Page navigation">
        <ul class="pagination justify-content-center">
            {$html}
        </ul>
    </nav>
HTML;
    }

    /**
      * Nb de Pages de toutes les actions futures d'un user
     * @param int $user_id user
     * @return float nb de pages
     */
    private function getNbPagesInIdToday(int $user_id): float
    {
        if ($this->count === null) {
            $this->count = $this->classTable->countToday($user_id)->nbrow;
        }
        if ($this->count === "0" || $this->count === 0.0 || $this->count === 0) {
            $this->count = 0.1;
        }
        return ceil($this->count / $this->perPage);
    }

    /**
     * Toutes les actions futures d'un user
     * @param int $user_id user
     * @return ?array les actions
     */
    public function getItemsInIdToday(int $user_id): ?array
    {
        $currentPage = $this->getCurrentPage();
        $nbPage = $this->getNbPagesInIdToday($user_id);

        if ($currentPage > $nbPage) {
            // page inexistante : page 1
            //throw new \Exception("pas de page");
            header('location: ' . $this->url);
            exit();
        }

        if ($this->items === null) {
            $offset = ($currentPage - 1) * $this->perPage;
            // lecture des éléments de la page dans la base
            $this->items = $this->classTable->allInIdByLimitToday($this->perPage, $offset, $user_id);
        }
        return ($this->items);
    }

    /**
       * Pages de toutes les actions futures d'un user
     * @param int $user_id user
     * @return array liste des pages
    */
    public function getNavInIdToday(int $user_id): array
    {
        $uri = $this->url;
        $nbPage = $this->getNbPagesInIdToday($user_id);
        $navArray = [];
        for ($i = 1; $i <= $nbPage; $i++) {
            $url = $i == 1 ? $uri : $uri . "?page=" . $i;
            $navArray[$i] = $url;
        }
        return $navArray;
    }

    /**
     * Pagination html de toutes les actions futures d'un user
     * @param int $user_id user
     * @return string liste des pages en html
     */
    public function getNavHtmlInIdToday(int $user_id): string
    {
        $urls = $this->getNavInIdToday($user_id);
        $html = "";
        foreach ($urls as $key => $url) {
            $class = $this->getCurrentPage() == $key ? " active" : "";
            $html .= "<li class=\"page-item {$class}\"><a class=\"page-link\" href=\"{$url}\">{$key}</a></li>";
        }
        return <<<HTML
        <nav class="Page navigation">
            <ul class="pagination justify-content-center">
                {$html}
            </ul>
        </nav>
HTML;
    }
}
