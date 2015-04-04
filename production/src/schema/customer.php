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

    function __construct($name, $botType){
        $this->name = $name;
        $this->botType = $botType;
    }
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getbotType()
    {
        return $this->botType;
    }

    public function setbotType($botType){
        $this->botType = $botType;

    }


}
