<?php

require_once __DIR__ . "/../../src/schema/customer.php";
require_once __DIR__ . "/../ormbootstrap.php";

abstract class MasterBot{
    const DEBUG = false;
    private $isInitialized = false;
    private $customerEntity;
    private $snapchat_engine;

    abstract protected function onNewFriendRequest($newFriends);

    function __construct(Customer $customerEntity){
        $this->customerEntity = $customerEntity;
    }

    function initialize(){
        $botUsername = $this->customerEntity->getBotUsername();
        $accountName = $this->customerEntity->getName();

        $this->snapchat_engine = new Snapchat($botUsername, self::DEBUG);
        $accountEntityManager = new ORMDBConnection($accountName);
        $this->isInitialized = true;
    }

    function start(){
        $this->startWithInterval(20);
    }

    function startForOneCycle(){
        $this->startWithInterval(30, true);
        return true;
    }

    function startWithInterval($Interval, $runOnce = false){
        if(!$this->isInitialized){
            throw new Exception('Must call .initialize() first');};

        $botPassword = $this->customerEntity->getBotPassword();

        while(true){
            $this->refreshToken();

            $newFriends = $this->getNewFriends();

            if(count($newFriends) > 0){

                foreach ($newFriends as $friend){

                    $this->onNewFriendRequest($friend);
                }
            }

            if($runOnce){break;}

            sleep($Interval);
        }
    }

    protected function refreshToken(){
        $this->snapchat_engine->login($botPassword);
    }

    protected function getNewFriends(){
        //Normalize for possible false value, if false return an empty array
        $thisCouldBeFalse = $this->snapchat_engine->getUncomfirmedFriends();
        if($thisCouldBeFalse == false){$thisCouldBeFalse = Array();}
        return $thisCouldBeFalse;
    }

    function getCustomerEntity(){
        return $this->customerEntity;
    }


}
?>
