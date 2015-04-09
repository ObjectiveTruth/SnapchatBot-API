<?php
require_once __DIR__ . "/ormbootstrap.php";
require_once __DIR__ . "/schema/customer.php";
require_once __DIR__ . "/ormbootstrap.php";
require_once __DIR__ . "/bots/posttostorybot.php";
require_once __DIR__ . "/bots/webmoderatedbot.php";
//require_once __DIR__ . "/constants.php";

class BotFactory{

    public function returnBotForAccount($accountName){
        $accountDBConnection = new ORMDBConnection("snapchatbot_db");
        $accountEntityManager = $accountDBConnection->getEntityManager();
        $customer = $accountEntityManager->find("Customer", $accountName);
        $customerBotType = $customer->getbotType();
        switch($customerBotType){
        case 0:
            return new PostToStoryBot($customer);
        case 1:
            return new WebModeratedBot($customer);
        default:
            throw new Exception("Bot Type: $customerBotType " . 
                "for customer $accountName not found");
        }
    }
}

?>
