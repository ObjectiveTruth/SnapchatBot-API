<?php
require_once __DIR__ . "/../../src/schema/customer.php";

class CustomerTest extends PHPUnit_Framework_TestCase{

    public function testGetSetName(){
        $customer = new Customer("bar", 1);

        $customer->setName("foo");

        $this->assertEquals("foo", $customer->getName());
    }

    public function testGetSetbotType(){
        $customer = new Customer("bar", 1);

        $customer->setbotType(2);

        $this->assertEquals(2, $customer->getbotType());
    }

    public function testConstructor(){
        $customer = new Customer("foo", 2);

        $this->assertEquals(2, $customer->getbotType());
        $this->assertEquals("foo", $customer->getName());
    }
}


?>
