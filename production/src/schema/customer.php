<?php
// src/customer.php
/**
 * @Entity @Table(name="MASTER_ACCOUNTS_CONFIG")
 **/
class Customer
{

    /** @accountname @Id @Column(type="string") **/
    protected $accountname;
    /** @bot_type @Column(type="integer") **/
    protected $bot_type;
    /** @bot_username @Column(type="string") **/
    protected $bot_username;
    /** @bot_password @Column(type="string") **/
    protected $bot_password;

    function __construct($accountname, $bot_type, $bot_username, $bot_password){
        $this->accountname= $accountname;
        $this->botType = $bot_type;
        $this->botUsername = $bot_username;
        $this->botPassword = $bot_password;
    }
    public function getbotUsername()    {return $this->botUsername;}
    public function setbotUsername($bot_username){self::$botUserName = $bot_username;}

    public function getbotPassword()    {return $this->botPassword;}
    public function setbotPassword($bot_password){self::$botUserName = $bot_password;}

    public function getAccountName()           {return $this->accountname;}
    public function setAccountName($accountname)      {$this->accountname= $accountname;}

    public function getbotType()        {return $this->bot_type;}
    public function setbotType($bot_type){$this->bot_type = $bot_type;}


}
