<?php

namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe ActionEntity : une action pour la récolte
 **/
class ActionEntity extends Entity
{
    /**
     * id
     */
    private $id;

    /**
     * id jardin
     */
    private $user_id;

    /**
     * titre
     */
    private $title;

    /**
     * contenu
     */
    private $content;

    /**
     * date limite
     */
    private $limited_at;

    /**
     *  id
     *  @return int
     **/
    public function getId(): int
    {
        return ($this->id);
    }

    /**
     *  user_id
     *  @return int
     **/
    public function getUserId(): int
    {
        return ($this->user_id);
    }

    /**
     *  title
     *  @return string
     **/
    public function getTitle(): string
    {
        return ((string) $this->title);
    }

    /**
     *  content
     *  @return string
     **/
    public function getContent(): string
    {
        return ((string) $this->content);
    }

    /**
     *  limited_at
     *  @return string
     **/
    public function getLimitedAt(): string
    {
        return ((string)$this->limited_at);
    }
        
    /**
     * getUrl()
     */
    public function getUrl(): string
    {
        return \App\App::getInstance()->getUri('action', [
            'id' => $this->getId()
        ]);
    }
}
