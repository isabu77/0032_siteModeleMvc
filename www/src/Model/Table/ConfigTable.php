<?php
namespace App\Model\Table;

use \Core\Model\Table;
use App\Model\Entity\ConfigEntity;

/**
 *  Classe ConfigTable : accès à la table Config
 **/
class ConfigTable extends Table
{
    /**
     * retourne le dernier id de la table (config de la date la plus récente)
     * @return int id
     */
    public function lastConfig()
    {
        $lastId = $this->query("SELECT MAX(id) as lastId FROM {$this->table}", null, true)->lastId;
        return $this->find($lastId);
    }
}
