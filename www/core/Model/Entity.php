<?php
namespace Core\Model;

/**
 *  Classe Entity : un entregistrement de table
 **/
class Entity
{

    public function __construct(array $post = [])
    {
        // remplir l'objet avec le formulaire posté en paramètre
        foreach ($post as $key => $value) {
            $methode = "set" . ucfirst($key);
            if (method_exists($this, $methode)) {
                $this->$methode($value);
            }
        }
    }
}
