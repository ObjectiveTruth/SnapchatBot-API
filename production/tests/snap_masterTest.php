<?php

require_once __DIR__ . "/../snap_master.php";

class StubTest extends PHPUnit_Framework_TestCase
{
    public function testStub()
    {
        $stub = $this->getMockBuilder('Snapchat')
            ->getMock();

        $stub->method('getFriends')
            ->willReturn('foo');


        $this->assertEquals('foo', $stub->getFriends());
    }
}




?>
