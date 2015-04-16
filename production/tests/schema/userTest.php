<?php
require_once __DIR__ . "/../../src/schema/user.php";

class userTest extends PHPUnit_Framework_TestCase{
    protected static $dummyUser;

    public static function setUpBeforeClass(){
        self::$dummyUser = new User("foo", 2);
    }

    public function testConstructor(){
        $this->assertEquals("foo", self::$dummyUser->getUsername());
        $this->assertEquals(2, self::$dummyUser->getPermission());
    }
    public function testGetSetUsername(){
        self::$dummyUser->setUsername("bar");

        $this->assertEquals("bar", self::$dummyUser->getUsername());
    }

    public function testGetSetPassword(){
        self::$dummyUser->setPassword("pass");

        $this->assertEquals("pass", self::$dummyUser->getPassword());
    }

    public function testGetSetPermission(){
        self::$dummyUser->setPermission(1);

        $this->assertEquals(1, self::$dummyUser->getPermission());
    }

    public function testGetSetEmail(){
        self::$dummyUser->setEmail("something@something.com");

        $this->assertEquals("something@something.com", 
            self::$dummyUser->getEmail());
    }
}


?>
