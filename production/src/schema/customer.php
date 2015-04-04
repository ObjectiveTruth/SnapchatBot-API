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

    function __construct($accountname, $bot_type){
        $this->accountname= $accountname;
        $this->bot_type = $bot_type;
    }
    public function getBotUsername()    {return $this->bot_username;}
    public function setBotUsername($bot_username){$this->bot_username = $bot_username;}

    public function getBotPassword()    {return $this->bot_password;}
    public function setBotPassword($bot_password){$this->bot_password = $bot_password;}

    public function getAccountName()           {return $this->accountname;}
    public function setAccountName($accountname)      {$this->accountname= $accountname;}

    public function getBotType()        {return $this->bot_type;}
    public function setBotType($bot_type){$this->bot_type = $bot_type;}


}
