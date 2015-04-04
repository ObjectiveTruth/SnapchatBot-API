<?php

require_once __DIR__ . "/../../src/schema/customer.php";
require_once __DIR__ . "/../ormbootstrap.php";

abstract class MasterBot{
    const DEBUG = false;
    private $isInitialized = false;
    private $customerEntity;
    private $snapchat_engine;

    abstract protected function onNewFriendRequest($newFriend);
    abstract protected function onNewSnap($snap);

    function __construct(Customer $customerEntity){
        $this->customerEntity = $customerEntity;
    }

    function initialize(){
        $botUsername = $this->customerEntity->getBotUsername();
        $accountName = $this->customerEntity->getAccountName();

        $this->snapchat_engine = new Snapchat($botUsername, self::DEBUG);
        $accountEntityManager = new ORMDBConnection($accountName);
        $this->isInitialized = true;
    }

    function start(){
        $this->startWithIntervalInSeconds(20);
    }

    function startForOneCycle(){
        $this->startWithIntervalInSeconds(30, true);
        return true;
    }

    function startWithIntervalInSeconds($Interval, $runOnce = false){
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

            $newSnaps = $this->getNewSnaps();
            if(count($newSnaps) > 0){

                foreach ($newSnaps as $snap){

                    $this->onNewSnap($snap);
                }
            }

            if($runOnce){break;}

            sleep($Interval);
        }
    }

    protected function refreshToken(){
        $botPassword = $this->customerEntity->getBotPassword();
        $this->snapchat_engine->login($botPassword);
    }

    protected function getNewFriends(){
        //Normalize for possible false value, if false return an empty array
        $snapchat_engine = $this->snapchat_engine;
        $thisCouldBeFalse = $snapchat_engine->getUncomfirmedFriends();
        if($thisCouldBeFalse == false){$thisCouldBeFalse = Array();}
        return $thisCouldBeFalse;
    }

    protected function getNewSnaps(){
        $accountName = $this->customerEntity->getAccountName();
        $newSnaps = $this->snapchat_engine->getSnaps(true, $accountName);
        //Returns false if something went wrong with the get request to snapchat
        if($newSnaps == false){throw new exception('Failed to get list of snaps');}
        return $newSnaps;

    }

    function getCustomerEntity(){
        return $this->customerEntity;
    }


}
?>
