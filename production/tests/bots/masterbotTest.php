<?php

require_once __DIR__ . "/../../src/bots/masterbot.php";
require_once __DIR__ . "/../../../src/snapchat.php";
require_once __DIR__ . "/../../src/schema/customer.php";

class basicTest extends PHPUnit_Framework_TestCase{
    protected static $dummyMasterBot;
    protected static $dummyCustomerEntity;

    public static function setUpBeforeClass(){
        self::$dummyCustomerEntity = new Customer("foo", 2, "username", "password");
        self::$dummyMasterBot = new DummyMasterBot(self::$dummyCustomerEntity);
    }

    public function testConstructorEqualsCustomer(){
        $this->assertEquals(self::$dummyCustomerEntity, 
            self::$dummyMasterBot->getCustomerEntity());

    }
    /**
     * @depends testConstructorEqualsCustomer
     * @expectedException   Exception
     * @expectedExceptionMessage Must call .initialize() first
     */
    public function testStart(){
        self::$dummyMasterBot->start();
    }

    /**
     * @depends testStart
     */
    public function testInitializeForDB(){
        self::$dummyMasterBot->initialize();
    }

    /**
     * @depends testInitializeForDB
     * @short
     */
    public function testStartOneCycle(){
        $mockMasterBot = $this->getMockBuilder('DummyMasterBot')
            ->setConstructorArgs(Array(self::$dummyCustomerEntity))
            ->getMock();

        $this->assertEquals(true, $mockMasterBot->startForOneCycle());
    }
     

public function invokeMethod(&$object, $methodName, array $parameters = array())
{
    $reflection = new \ReflectionClass(get_class($object));
    $method = $reflection->getMethod($methodName);
    $method->setAccessible(true);

    return $method->invokeArgs($object, $parameters);
}
}

//Implementation of MasterBot for testing
class DummyMasterBot extends MasterBot{
    protected function onNewFriendRequest($newFriends){
    }
    protected function refreshToken(){
    }
    protected function getNewFriends(){
        return Array("Alex", "Caleb", "Elias", "Thomas", "Anthony");
    }

}


?>
