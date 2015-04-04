<?php

require_once __DIR__ . "/../../src/schema/customer.php";
require_once __DIR__ . "/../ormbootstrap.php";

abstract class MasterBot{
    const DEBUG = false;
    private static $isInitialized = false;
    private static $customerEntity;
    private static $snapchat_engine;

    abstract protected function onNewFriend($newFriends);

    function __construct(Customer $customerEntity){
        $this->customerEntity = $customerEntity;
    }

    function initialize(){
        $botUsername = $this->customerEntity->getBotUsername();
        $botPassword = $this->customerEntity->getBotPassword();

        self::$snapchat_engine = new Snapchat($botUsername, self::DEBUG);
        $accountDBConnection = new ORMDBConnection($accountName);
        $accountEntityManager = $accountDBConnection->getEntityManager();
    }

    function start(){
        if(!self::$isInitialized){
            throw new Exception('Must call .initialize() first');};
    }

    function getCustomerEntity(){
        return $this->customerEntity;
    }

    public function getFriends(){
        return 'foo';
    }

}
?>
