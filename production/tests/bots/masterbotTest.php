<?php

require_once __DIR__ . "/../../src/bots/masterbot.php";
require_once __DIR__ . "/../../../src/snapchat.php";

class basicTest extends PHPUnit_Framework_TestCase{

    public function (){

        $masterBot = new MasterBot();

        $this->assertEquals('foo', $masterBot->getFriends());


    }
}


?>
