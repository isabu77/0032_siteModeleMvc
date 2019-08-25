<?php

namespace Core\Controller;

class FormController
{
    private $postDatas = [];
    private $datas = [];
    private $fields = [];
    private $errors = [];

    /**
     * constructeur : remplit postDatas (données saisies par $_POST)
     */
    public function __construct(array $post = [])
    {
        if (count($post) == 0) {
            $post = $_POST;
        }
        if (count($post) > 0) {
            $this->postDatas = $post;
        } else {
            $this->errors["post"] = "no-data";
        }
    }
    /**
     * pour ne pas oublier l'échappement des caractères
     * dans la récupération du champ saisi
     */
    private function addToDatas(string $key, $data = null): void
    {
        if (is_null($data)) {
            $data = htmlspecialchars($this->postDatas[$key]);
        }
        $this->datas[$key] = $data;
    }

    /**
     * vérifie la contrainte "require"
     */
    private function fieldRequire(string $field, bool $value)
    {
        if (!empty($this->postDatas[$field])) {
            // associer valeurs de fields avec data
            $this->addToDatas($field);
            //$this->datas[$field] = htmlspecialchars($this->postDatas[$field]);
        } else {
            unset($this->datas[$field]);
            $this->errors[$field] = "Le champ {$field} ne peut pas être vide";
        }
    }

    /**
     * vérifie la contrainte "verify"
     */
    private function fieldVerify(string $field, bool $value)
    {
        if (!empty($this->postDatas[$field . 'Verify'])) {
            if ($this->postDatas[$field] == $this->postDatas[$field . 'Verify']) {
                // associer valeurs de fields avec data
                $this->addToDatas($field);
                //$this->datas[$field] = htmlspecialchars($this->postDatas[$field]);
            } else {
                unset($this->datas[$field]);
                $this->errors[$field] = "Les champs {$field} doivent correspondre";
            }
        } else {
            unset($this->datas[$field]);
            $this->errors[$field] = "Le champ {$field} de vérification ne peut pas être vide";
        }
    }

    /**
     * vérifie la contrainte "length"
     */
    private function fieldLength(string $field, int $value)
    {
        //echo " value = " . $value;
        //echo " strlen = " . strlen($this->postDatas[$field]);
        if (strlen($this->postDatas[$field]) >= $value) {
            // associer valeurs de fields avec data
            $this->addToDatas($field);
            //$this->datas[$field] = htmlspecialchars($this->postDatas[$field]);
        } else {
            unset($this->datas[$field]);
            $this->errors[$field] = "Le champ {$field} doit avoir au minimum {$value} caractères";
        }
    }

    /**
     * enregistre un champ de saisie dans le tableau fields avec ses contraintes
     */
    public function field(string $field, array $constraints = [])
    {
        // pour les contraintes 'require' et 'verify' :
        // remplace la clé par la valeur avec true en valeur
        foreach ($constraints as $key => $value) {
            if (!is_string($key)) {
                unset($constraints[$key]);
                $constraints[$value] = true;
            }
        }
        
        // les contraintes du champ
        $this->fields[$field] = $constraints;

        // si pas de contraintes : on met le champ dans les datas
        if (empty($constraints)) {
            $this->addToDatas($field);
            //$this->datas[$field] = htmlspecialchars($this->postDatas[$field]);
        }

        return $this;
    }

    /**
     * retourne les erreurs de saisie de tous les champs de l'objet
     * en fonction des contraintes souhaitées
     */
    public function hasErrors(): array
    {
        if (!isset($this->errors['post']) || !$this->errors['post']) {
            foreach ($this->fields as $field => $constraints) {
                // vérifier les contraintes de tous les champs
                foreach ($constraints as $key => $value) {
                    // chercher la méthode 'field' associée à la contrainte par sa clé
                    $constraintMethode = "field" . ucfirst(strtolower($key));
                    if (method_exists($this, $constraintMethode)) {
                        $this->$constraintMethode($field, $value);
                    } else {
                        throw new \Exception("La contrainte $key n'existe pas", 1);
                    }
                }
            }
        }
        return $this->errors;
    }

    /**
     * retourne les données saisies dans le formulaire, vérifiées par hasErrors()
     */
    public function getDatas(): array
    {
        return $this->datas;
    }
}
