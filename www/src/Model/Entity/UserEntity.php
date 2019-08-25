<?php
namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe UserEntity : un utilisateur
 **/
class UserEntity extends Entity
{
    /**
     * id
     */
    private $id;

    /**
     * mail
     */
    private $mail;

    /**
     * password
     */
    private $password;

    /**
     * token
     */
    private $token;

    /**
     * created_at
     */
    private $created_at;

    /**
     * verify
     */
    private $verify;

    /**
     *  id
     *  @return int
     **/
    public function getId(): int
    {
        return ((int)$this->id);
    }

    /**
     *  contenu
     *  @return string
     **/
    public function getMail(): string
    {
        return ((string)$this->mail);
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function getPassword(): string
    {
        return ((string)$this->password);
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function getToken(): string
    {
        return ((string)$this->token);
    }
        
    /**
     *  contenu
     *  @return string
     **/
    public function getCreatedAt(): string
    {
        return ((string)$this->created_at);
    }
        
     /**
     *  contenu
     *  @return int
     **/
    public function getVerify(): int
    {
        return ((int)$this->verify);
    }
    
    /**
     * getUrl()
     *  @return string
     */
    public function getUrl():string
    {
        return \App\App::getInstance()->getUri('users', [
            'id' => $this->getId()
            ]);
    }
            
    /**
     *  mail
     *  @param $mail string
     **/
    public function setMail(string $mail)
    {
        $this->mail = $mail;
    }
/**
     *  password
     *  @param $password string
     **/
    public function setPassword(string $password)
    {
        $this->password = $password;
    }
    /**
     *  created_at
     *  @param $created_at string
     **/
    public function setCreatedAt(string $created_at)
    {
        $this->created_at = $created_at;
    }
}
