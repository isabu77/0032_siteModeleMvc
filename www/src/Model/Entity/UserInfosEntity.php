<?php
namespace App\Model\Entity;

use \Core\Controller\Helpers\TextController;
use \Core\Model\Entity;

/**
 *  Classe UserInfosEntity : les coordonnées d'un utilisateur
 **/
class UserInfosEntity extends Entity
{
    /**
     * id
     */
    private $id;

    /**
     * user_id
     */
    private $user_id;

    /**
     * lastname
     */
    private $lastname;

    /**
     * firstname
     */
    private $firstname;

    /**
     * address
     */
    private $address;

    /**
     * city
     */
    private $city;

    /**
     * zip_code
     */
    private $zip_code;

    /**
     * country
     */
    private $country;

    /**
     * phone
     */
    private $phone;

    /**
     * Tableau des propriétés
     * @return array
     */
    public function getProperties(): array
    {
        return get_object_vars($this);
    }
    /**
     *  id
     *  @return int
     **/
    public function getId(): int
    {
        return ((int)$this->id);
    }

    /**
     *  user_id
     *  @return int
     **/
    public function getUserId(): int
    {
        return ((int)$this->user_id);
    }

    /**
     *  lastname
     *  @return string
     **/
    public function getLastname(): string
    {
        return ((string)$this->lastname);
    }

    /**
     *  firstname
     *  @return string
     **/

    public function getFirstname(): string
    {
        return ((string)$this->firstname);
    }

    /**
     *  zip_code
     *  @return string
     **/
    public function getZipCode(): string
    {
        return ((string)$this->zip_code);
    }
    /**
     *  address
     *  @return string
     **/
    public function getAddress(): string
    {
        return ((string)$this->address);
    }
        
    /**
     *  city
     *  @return string
     **/
    public function getCity(): string
    {
        return ((string)$this->city);
    }
        
    /**
     *  country
     *  @return string
     **/
    public function getCountry(): string
    {
        return ((string)$this->country);
    }
        
    /**
     *  phone
     *  @return string
     **/
    public function getPhone(): string
    {
        return ((string)$this->phone);
    }
        
    /**
     * getUrl()
     */
    public function getUrl(): string
    {
        return \App\App::getInstance()->getUri("profil_post", [
            'id' => $this->getId()
            ]);
    }
    /**
     *  id
     *  @param $id int
     **/
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     *  user_id
      *  @param $user_id int
     **/
    public function setUserId(string $user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     *  lastname
      *  @param $lastname string
     **/
    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
    }

    /**
     *  firstname
      *  @param $firstname string
     **/

    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }

    /**
     *  zip_code
      *  @param $zip_code string
     **/
    public function setZipCode(string $zip_code)
    {
        $this->zip_code = $zip_code;
    }
    /**
     *  address
      *  @param $address string
     **/
    public function setAddress(string $address)
    {
        $this->address = $address;
    }
        
    /**
     *  city
      *  @param $city string
     **/
    public function setCity(string $city)
    {
        $this->city = $city;
    }
        
    /**
     *  country
      *  @param $country string
     **/
    public function setCountry(string $country)
    {
        $this->country = $country;
    }
        
    /**
     *  phone
      *  @param $phone string
     **/
    public function setPhone(string $phone)
    {
        $this->phone = $phone;
    }
}
