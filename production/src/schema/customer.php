<?php
// src/customer.php
/**
 * @Entity @Table(name="MASTER_ACCOUNTS_CONFIG")
 **/
class Customer
{

    /** @accountname @Id @Column(type="string") **/
    protected $name;
    /** @bot_type @Column(type="integer") **/
    protected $botType;
    /** @bot_type @Column(type="string") **/
    protected $botUsername;
    /** @bot_type @Column(type="string") **/
    protected $botPassword;

    function __construct($name, $botType, $botUsername, $botPassword){
        $this->name = $name;
        $this->botType = $botType;
        $this->botUsername = $botUsername;
        $this->botPassword = $botPassword;
    }
    public function getbotUsername()    {return $this->botUsername;}
    public function setbotUsername($botUsername){self::$botUserName = $botUsername;}

    public function getbotPassword()    {return $this->botPassword;}
    public function setbotPassword($botPassword){self::$botUserName = $botPassword;}

    public function getName()           {return $this->name;}
    public function setName($name)      {$this->name = $name;}

    public function getbotType()        {return $this->botType;}
    public function setbotType($botType){$this->botType = $botType;}


}
