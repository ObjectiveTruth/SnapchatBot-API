<?php
require_once __DIR__ . "/../../src/schema/domain.php";

class DomainTest extends PHPUnit_Framework_TestCase{
    protected static $dummyDomain;

    public function testConstructor(){
        self::$dummyDomain = new Domain("foo", 2);

        $this->assertEquals("foo", self::$dummyDomain->getDomainName());
        $this->assertEquals(2, self::$dummyDomain->getBotType());
    }

    public function testGetSetName(){
        self::$dummyDomain->setDomainName("bar");

        $this->assertEquals("bar", self::$dummyDomain->getDomainName());
    }

    public function testGetSetBotType(){

        self::$dummyDomain->setBotType(2);

        $this->assertEquals(2, self::$dummyDomain->getBotType());
    }

    public function testGetSetBotUsername(){
        self::$dummyDomain->setBotUsername("username");

        $this->assertEquals("username", self::$dummyDomain->getBotUsername());
    }

    public function testGetSetBotPassword(){
        self::$dummyDomain->setBotPassword("password");

        $this->assertEquals("password", self::$dummyDomain->getBotPassword());
    }

}


?>
