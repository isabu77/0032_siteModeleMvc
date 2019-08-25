<?php
namespace App\Model\Entity;

use \Core\Model\Entity;

/**
 *  Classe ConfigEntity : configuration générale
 **/
class ConfigEntity extends Entity
{
    /**
     * id
     */
    private $id;

    /**
     * date
     */
    private $created_at;

    /**
     * tva
     */
    private $tva;

    /**
     * port
     */
    private $port;

    /**
     * limite pour port
     */
    private $ship_limit;

    /**
     * delai de pousse en jours par défaut
     */
    private $growing_days;

    /**
     *  id
     *  @return int
     **/
    public function getId(): int
    {
        return ((int)$this->id);
    }

   /**
     *  created_at
     *  @return string
     **/
    public function getCreatedAt(): string
    {
        return ((string)$this->created_at);
    }

     /**
     *  tva
     *  @return float
     **/
    public function getTva(): float
    {
        return ((float)$this->tva);
    }
    /**
     *  port
     *  @return float
     **/
    public function getPort(): float
    {
        return ((float)$this->port);
    }
    /**
     *  ship_limit
     *  @return float
     **/
    public function getShipLimit(): float
    {
        return ((float)$this->ship_limit);
    }
        
    /**
     *  growing_days
     *  @return int
     **/
    public function getGrowingDays(): int
    {
        return ($this->growing_days);
    }

    
    /**
     * getUrl()
     *  @return string
     */
    public function getUrl():string
    {
        return \App\App::getInstance()->getUri('config', [
            'id' => $this->getId()
            ]);
    }
            
    /**
     *  tva
     * @param float $tva TVA
     *  @return void
     **/
    public function setTva(float $tva)
    {
        $this->tva = $tva;
    }
    /**
     *  port
     * @param float $port Frais de port
     *  @return void
     **/
    public function setPort(float $port)
    {
        $this->port = $port;
    }
    /**
     *  ship_limit
     * @param float $ship_limit Minimum pour frais de port
     *  @return void
     **/
    public function setShipLimit(float $ship_limit)
    {
        $this->ship_limit = $ship_limit;
    }
}
