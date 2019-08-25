<?php
namespace Core\Model;

use \Core\Controller\Database\DatabaseController;

/**
 *  Classe Table : accès aux tables (classe abstraite ne pouvant pas être instanciée seule)
 **/
abstract class Table
{
    protected $prefix;

    /**
     * @var db : DatabaseController
     * @access protected
     */
    protected $db;

    /**
     * @var table : nom de la table en base
     * @access protected
     */
    protected $table;

    /**
     * Constructeur de la classe avec un databaseController
     *
     * @return void
     * @access private
     */
    public function __construct(DatabaseController $db = null)
    {
        $this->db = $db;
        $this->prefix = (string)\App\App::getInstance()->getEnv('TABLE_PREFIX');

        if (is_null($this->table)) {
            $this->table = $this->extractTableName();
        }
    }

    /**
     * extractTableName : construit le nom de la table à partir du nom de la classe
     * en otant "Table" à la fin du nom
     * en ajoutant un '_' devant chaque majuscule (sauf si c'est la première lettre)
     */
    public function extractTableName(): string
    {
            // App\Model\Table\ClassMachinTable
            $parts = explode('\\', get_class($this));
            // [ "App", "Model", "Table", "ClassMachinTable"]

            // nom de la classe dont on crée une instance : ClassMachinTable
            $class_name = end($parts);

            // retirer "Table" pour obtenir le nom de la table en base : ClassMachin
            $class_name = str_replace('Table', '', $class_name);

            // insérer l'underscore dans le nom de table s'il y a une majuscule :
            $new_name = $this->prefix.$class_name[0];
        for ($i = 1; $i < strlen($class_name); $i++) {
            if (ctype_upper($class_name[$i])) {
                $new_name .= '_';
            }
            $new_name .= $class_name[$i];
        }
            // mettre en minuscules : class_machin
            return strtolower($new_name);
    }
    /**
     * exécution de la requête à la base
     */
    public function query(string $statement, ?array $attributes = null, bool $one = false, ?string $class_name = null)
    {
        if (is_null($class_name)) {
            // instance de la classe 'Entity'
            $class_name = str_replace('Table', 'Entity', get_class($this));
        }
        if ($attributes) {
            return $this->db->prepare($statement, $attributes, $class_name, $one);
        } else {
            return $this->db->query($statement, $class_name, $one);
        }
    }

    /**
     * retourne le nombre d'items de la table
     */
    public function count(?int $id = null)
    {
        return $this->query("SELECT COUNT(id) as nbrow FROM $this->table", null, true, null);
    }
    
    /**
     * retourne le dernier id de la table
     */
    public function last()
    {
        return $this->query("SELECT MAX(id) as lastId FROM {$this->table}", null, true)->lastId;
    }

    /**
     * lecture de tous les enregistrements d'une table par page
     */
    public function allByLimit(int $limit, int $offset)
    {
        return $this->query("SELECT * FROM {$this->table} LIMIT {$limit} OFFSET {$offset}");
    }

    /**
     * lecture de tous les enregistrement d'une table
     */
    public function all()
    {
        return $this->query("SELECT * FROM {$this->table}");
    }

    /**
     * lecture d'un enregistrement par son id
     * valable pour n'importe quelle table
     */
    public function find(int $id)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE id=?", [$id], true);
    }

    public function findBy(string $what, string $attributes, bool $one = false)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE $what = ?", [$attributes], $one);
    }

    public function lastThird()
    {
        return $this->query("SELECT * FROM {$this->table} ORDER BY id DESC LIMIT 3", null, false, null);
    }

    public function latestById()
    {
        $id = $this->query("SELECT id FROM {$this->table} ORDER BY id DESC LIMIT 1", null, true, null);
        if ($id) {
            $id = $id->getId();
            return $this->query("SELECT * FROM {$this->table} WHERE id = ?", [$id], true, null);
        } else {
            return null;
        }
    }

    public function allWithoutLimit()
    {
        return $this->query("SELECT * FROM {$this->table} ORDER BY id");
    }

    /**
     * lecture d'un enregistrement par son id
     * valable pour n'importe quelle table
     */
    public function delete(int $id)
    {
        return $this->query("DELETE FROM {$this->table} WHERE id=?", [$id]);
    }

    /**
     * insert d'un enregistrement par son id
     * valable pour n'importe quelle table
     */
    public function insert($fields)
    {
        $sql_parts = [];
        $attributes = [];
        foreach ($fields as $k => $v) {
            $sql_cols[] = "$k";
            $sql_parts[] = ":$k";
            $attributes[":$k"] = $v;
        }
        $sql_part = implode(', ', $sql_parts);
        $sql_col = implode(', ', $sql_cols);
        return $this->query("INSERT INTO {$this->table} ({$sql_col}) VALUES ({$sql_part})", $attributes);
    }
    /**
     * update d'un enregistrement par son id
     * valable pour n'importe quelle table
     */
    public function update($id, $fields)
    {
        $sql_parts = [];
        $attributes = [];
        foreach ($fields as $k => $v) {
            $sql_parts[] = "$k = ?";
            $attributes[] = $v;
        }
        $attributes[] = $id;
        $sql_part = implode(', ', $sql_parts);
        $sql = "UPDATE {$this->table} SET $sql_part WHERE id = ?";

        return $this->query("UPDATE {$this->table} SET $sql_part WHERE id = ?", $attributes, true);
    }

    /**
     * update d'une colonne d'un enregistrement par son id
     * valable pour n'importe quelle table
     */
    public function updateColumn(string $column, string $news, int $id)
    {
        return $this->db->query("UPDATE {$this->table} SET $column = '$news'  WHERE id = $id");
    }
}
