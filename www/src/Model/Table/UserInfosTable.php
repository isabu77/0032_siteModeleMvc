<?php

namespace App\Model\Table;

use \Core\Model\Table;
use \Core\Controller\Helpers\TextController;
use App\Model\Entity\UserInfosEntity;

/**
 *  Classe UserInfosTable : accès à la table client
 **/
class UserInfosTable extends Table
{
    /**
     * cherche les clients associés à un id user
     * @param int $user_id id utilisateur
     * @return boolean|object
     */
    public function getUserInfosByUserId($user_id)
    {
        return $this->query(" SELECT * FROM {$this->table} WHERE `user_id`  = {$user_id}");
    }
}
