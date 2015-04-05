<?php
require_once __DIR__ . "/../../src/schema/customer.php";

class CustomerTest extends PHPUnit_Framework_TestCase{
    protected static $dummyCustomer;

    public function testConstructor(){
        self::$dummyCustomer = new Customer("foo", 2);

        $this->assertEquals("foo", self::$dummyCustomer->getAccountName());
        $this->assertEquals(2, self::$dummyCustomer->getBotType());
    }

    public function testGetSetName(){
        self::$dummyCustomer->setAccountName("bar");

        $this->assertEquals("bar", self::$dummyCustomer->getAccountName());
    }

    public function testGetSetBotType(){

        self::$dummyCustomer->setBotType(2);

        $this->assertEquals(2, self::$dummyCustomer->getBotType());
    }

    public function testGetSetBotUsername(){
        self::$dummyCustomer->setBotUsername("username");

        $this->assertEquals("username", self::$dummyCustomer->getBotUsername());
    }

    public function testGetSetBotPassword(){
        self::$dummyCustomer->setBotPassword("password");

        $this->assertEquals("password", self::$dummyCustomer->getBotPassword());
    }

    public function testGetSetDefaultFriendPermission(){
        self::$dummyCustomer->setDefaultFriendPermission(2);

        $this->assertEquals(2, self::$dummyCustomer->getDefaultFriendPermission());
    }

}


?>
