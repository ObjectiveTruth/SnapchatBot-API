<?php

require_once __DIR__ . "/../../src/schema/customer.php";
require_once __DIR__ . "/../ormbootstrap.php";
require_once __DIR__ . "/../../src/schema/friend.php";

abstract class MasterBot{
    const DEBUG = false;
    const PERMISSION_CAN_POST = 0;
    const PERMISSION_CANNOT_POST = 1;
    private $isInitialized = false;
    private $customerEntity;
    private $snapchat_engine;

    abstract protected function onNewFriendRequest($newFriend);
    abstract protected function onNewSnap($snap);
    abstract protected function getDefaultFriendPermission();

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
        $this->startWithIntervalInSeconds(20, true);
        return true;
    }

    function startWithIntervalInSeconds($Interval, $runOnce = false){
        if(!$this->isInitialized){
            throw new Exception('Must call .initialize() first');};

        $botPassword = $this->customerEntity->getBotPassword();

        $isFirstCycle = true;

        while(true){
            //Must be called before the rest
            $this->refreshToken();

            if($isFirstCycle){
                $isFirstCycle = false;
                $currentFriends = $this->getCurrentFriends();
                if(count($currentFriends) > 0){
                    foreach ($currentFriends as $friend){
                        $this->saveFriendByNameToDBWithDefaults($friend);
                    }
                }
            }

            $newFriends = $this->getNewFriends();

            if(count($newFriends) > 0){

                foreach ($newFriends as $friend){

                    $this->onNewFriendRequest($friend);
                }
            }

            $newSnapObjs = $this->getNewSnaps();
            if(count($newSnapObjs) > 0){

                foreach ($newSnapObjs as $snapObj){

                    $this->onNewSnap($snapObj);
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
        $thisCouldBeFalse = $snapchat_engine->getAddedFriends();
        if($thisCouldBeFalse == false){$thisCouldBeFalse = Array();}
        return $thisCouldBeFalse;
    }

    protected function getCurrentFriends(){
        $friendslist = $snapchat_engine = $this->snapchat_engine
            ->getFriends();
        if($friendslist == false){$friendslist = Array();}
        return $friendslist;
    }

    protected function getNewSnaps(){
        $accountName = $this->customerEntity->getAccountName();
        $newSnaps = $this->snapchat_engine->getSnaps(true, $accountName);
        //Returns false if something went wrong with the get request to snapchat
        if($newSnaps == false){$newSnaps = Array();}
        return $newSnaps;
    }

    protected function addFriendByName($friendName){
        $snapchat_engine = $this->snapchat_engine
            ->addFriend($friendName);
    }
    
    protected function markSnapIdAsViewed($id, $time=1){
        $snapchat_engine = $this->snapchat_engine
            ->markSnapViewed($id, $time);
    }

    protected function postSnapToStoryByFilename($snapFileName){
        $snapchat_engine = $this->snapchat_engine
            ->setStory($snapFileName);
    }

    protected function saveFriendByNameToDBWithDefaults($newFriendName){
        //Only adds if entry doesn't already exist
        $accountDBConnection = new ORMDBConnection($this->getAccountName());
        $accountEntityManager = $accountDBConnection->getEntityManager();
        $friend = $accountEntityManager->find("Friend", $newFriendName);

        if($friend == null){

            $friend = new Friend($newFriendName, 
                $this->getDefaultFriendPermission());
            $accountEntityManager->persist($friend);
            $accountEntityManager->flush();
        }
    }

    protected function getPermissionForFriendByUsername($friendName){
        $accountDBConnection = new ORMDBConnection($this->getAccountName());
        $accountEntityManager = $accountDBConnection->getEntityManager();
        return $accountEntityManager->find("Friend", $friendName)
            ->getPermission();
    }

    protected function getAccountName(){
        return $this->customerEntity->getAccountName();
    }

    function getCustomerEntity(){
        return $this->customerEntity;
    }


}
?>
