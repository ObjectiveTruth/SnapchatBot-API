<?php
require_once __DIR__ . "/../../src/schema/customer.php";

class CustomerTest extends PHPUnit_Framework_TestCase{
    protected static $dummyCustomer;

    public function testConstructor(){
        self::$dummyCustomer = new Customer("foo", 2, "username", "password");

        $this->assertEquals(2, self::$dummyCustomer->getbotType());
        $this->assertEquals("foo", self::$dummyCustomer->getName());
    }

    /*  
     *  @depends testConstructor
     */
    public function testGetSetName(){
        self::$dummyCustomer->setName("bar");

        $this->assertEquals("bar", self::$dummyCustomer->getName());
    }

    /*
     *  @depends testGetSetName
     */
    public function testGetSetbotType(){

        self::$dummyCustomer->setbotType(2);

        $this->assertEquals(2, self::$dummyCustomer->getbotType());
    }

}


?>
