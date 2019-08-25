<?php
namespace App\Model\Table;

use \Core\Model\Table;
use \Core\Controller\Helpers\TextController;

use phpDocumentor\Reflection\Types\Boolean;

/**
 *  Classe UserTable : accès à la table Users
 **/
class UserTable extends Table
{
    /**
     * retourne l'utilisateur après vérification de son mot de passe
     * @param string $mail email
     * @param string $password password
     */
    public function getUser($mail, $password)
    {
        $user = $this->query("SELECT * FROM $this->table 
            WHERE mail = ?", [$mail], true);
        if ($user) {
            if (password_verify($password, $user->getPassword())) {
                $user->setPassword('');
                return $user;
            }
        }
        return false;
    }
    /**
     * cherche l'utilisateur par son mail
     * @param string $mail email
     * @return boolean|object
     */
    public function getUserByMail($mail): ?object
    {
        $user = $this->query(" SELECT * FROM {$this->table} WHERE `mail`  = ?", [$mail], true);

        if ($user) {
            return $user;
        } else {
            return null;
        }
    }
    /**
     * supprime le token d'un utilisateur
     * @param int $id id utilisateur
     */
    public function deleteToken($id)
    {
        return $this->query("UPDATE {$this->table} SET token = '' WHERE id = ?", [$id]);
    }

    /**
     * crée un utilisateur
     * @param array $datas données
     */
    public function newUser(array $datas): Boolean
    {

        $sqlParts = [];
        foreach ($datas as $nom => $value) {
            $sqlParts[] = "$nom = :$nom";
        }

        $statement = "INSERT INTO {$this->table} SET ". join(',', $sqlParts);
        return $this->query($statement, $datas);
    }
}
