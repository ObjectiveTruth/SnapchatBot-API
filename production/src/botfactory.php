<?php
require_once __DIR__ . "/ormbootstrap.php";
require_once __DIR__ . "/schema/domain.php";
require_once __DIR__ . "/ormbootstrap.php";
require_once __DIR__ . "/bots/posttostorybot.php";
require_once __DIR__ . "/bots/webmoderatedbot.php";
//require_once __DIR__ . "/constants.php";

class BotFactory{

    public function returnBotForAccount($domainName){
        $domainDBConnection = new ORMDBConnection("snapchatbot_db");
        $domainEntityManager = $domainDBConnection->getEntityManager();
        $domain = $domainEntityManager->find("Customer", $domainName);
        $domainBotType = $domain->getbotType();
        switch($domainBotType){
        case 0:
            return new PostToStoryBot($domain);
        case 1:
            return new WebModeratedBot($domain);
        default:
            throw new Exception("Bot Type: $domainBotType " . 
                "for domain $domainName not found");
        }
    }
}

?>
