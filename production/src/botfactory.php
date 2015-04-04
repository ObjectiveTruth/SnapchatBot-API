<?php
require_once __DIR__ . "/ormbootstrap.php";
require_once __DIR__ . "/schema/customer.php";
require_once __DIR__ . "/ormbootstrap.php";
require_once __DIR__ . "/bots/posttostorybot.php";
//require_once __DIR__ . "/constants.php";

class BotFactory{

    public function returnBotForAccount($accountName){
        $accountDBConnection = new ORMDBConnection("snapchatbot_db");
        $accountEntityManager = $accountDBConnection->getEntityManager();
        $customer = $accountEntityManager->find("Customer", $accountName);
        if($customer->getbotType() == 0){
            return new posttostorybot($customer);
        }
        print_r($repository);

    }
}

?>
