<?php
namespace App\Model\Table;

use \Core\Model\Table;
use App\Model\Entity\ActionEntity;
use Faker\Provider\DateTime;

/**
 *  Classe ActionTable : accès à la table action
 **/
class ActionTable extends Table
{
    /**
     * lecture de tous les enregistrement d'une table 
     * groupés par contenu pour oter les doublons
     */
    public function allGroupedByTitle()
    {
        return $this->query("SELECT title,id FROM {$this->table} GROUP BY title,id");
    }

    /**
     * lecture de tous les enregistrement d'une table 
     * groupés par contenu pour oter les doublons
     */
    public function allGroupedByContent()
    {
        return $this->query("SELECT content,id FROM {$this->table} GROUP BY content,id");
    }

    /**
     * surcharge de count() pour gérer le nb d'actions d'un user
     * @param ?int $user_id user
     */
    public function count(?int $user_id = null)
    {
        if (!$user_id) {
            // sans id : appel de la méthode de la classe parente Table.php
            return parent::count();
        } else {
            return $this->query("SELECT COUNT(id) as nbrow FROM {$this->table} 
                    WHERE user_id = {$user_id}", null, true);
        }
    }

    /**
     * lecture de toutes les actions d'un user d'une page
     * @param int $limit nb par page
     * @param int $offset numéro de page
     * @param int $user_id user
     * @return array toutes les actions d'un user d'une page
     */
    public function allInIdByLimit(int $limit, int $offset, int $user_id): array
    {
        $sql = "SELECT * FROM {$this->table} 
        WHERE user_id = {$user_id} 
        ORDER BY limited_at ASC
        LIMIT {$limit} OFFSET {$offset} ";
        return $this->query($sql, null);
    }

    /**
     * nb d'actions futures d'un user à faire
     * @param ?int $user_id user
     */
    public function countToday(?int $user_id = null)
    {
        if (!$user_id) {
            // sans id : appel de la méthode de la classe parente Table.php
            return parent::count();
        } else {
            return $this->query("SELECT COUNT(id) as nbrow FROM {$this->table} 
                    WHERE user_id = {$user_id}", null, true);
        }
    }

    /**
     * lecture de toutes les actions futures d'un user d'une page
     * @param int $limit nb par page
     * @param int $offset numéro de page
     * @param int $user_id user
     * @return array toutes les actions futures d'un user d'une page
     */
    public function allInIdByLimitToday(int $limit, int $offset, int $user_id): array
    {
        $today = date('Y-m-d');
        $sql = "SELECT * FROM {$this->table} 
        WHERE user_id = {$user_id} 
        AND limited_at >= '{$today}'
        ORDER BY limited_at ASC
        LIMIT {$limit} OFFSET {$offset} ";
        return $this->query($sql, null);
    }
}
