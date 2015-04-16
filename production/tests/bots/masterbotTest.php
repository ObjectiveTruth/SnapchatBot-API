<?php

require_once __DIR__ . "/../../src/bots/masterbot.php";
require_once __DIR__ . "/../../../src/snapchat.php";
require_once __DIR__ . "/../../src/schema/domain.php";

class basicTest extends PHPUnit_Framework_TestCase{
    protected static $dummyMasterBot;
    protected static $dummyDomainEntity;

    public static function setUpBeforeClass(){
        self::$dummyDomainEntity = new Domain("Miguel", 2, "username", "password");
        self::$dummyMasterBot = new DummyMasterBot(self::$dummyDomainEntity);
    }
    public static function tearDownAfterClass(){
        $testFile = "objectivetruth_546915428790740918r";
        $webmDir = __DIR__ . "/../../../webmtemp/_Miguel";
        $pathToFinalWebmFile = $webmDir."/$testFile.webm";
        if(file_exists($pathToFinalWebmFile)){
            unlink($pathToFinalWebmFile);
        }
        if(is_dir($webmDir)){
            @rmdir($webmDir);
        }
    }
    public function testConstructorEqualsDomain(){
        $this->assertEquals(self::$dummyDomainEntity, 
            self::$dummyMasterBot->getDomainEntity());

    }
    public function testGetDomainName(){
        $this->assertEquals(
            $this->invokeMethod(self::$dummyMasterBot, 'getDomainName'), "Miguel");
    }

    public function testGetDefaultFriendPermission(){
        $this->assertEquals(
            $this->invokeMethod(self::$dummyMasterBot, 
            'getDefaultFriendPermission'), 0);
    }

    /**
     * @depends testConstructorEqualsDomain
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
    public function testonFriendRequestCalledForNewFriendsArraySize(){
        $mock = $this->getMockBuilder('DummyMasterBot')
            ->setConstructorArgs(Array(self::$dummyDomainEntity))
            ->setMethods(array('onNewSnap', 'onNewFriendRequest', 
                'saveFriendByNameToDBWithDefaults'))
            ->getMock();

        $mock->expects($this->exactly(5))->method('onNewFriendRequest');

        $mock->initialize();
        $mock->startForOneCycle();
    }

    /**
     * @depends testInitializeForDB
     * @short
     */
    public function testsaveFriendByNameToDBWithDefaults(){
        $mock = $this->getMockBuilder('DummyMasterBot')
            ->setConstructorArgs(Array(self::$dummyDomainEntity))
            ->setMethods(array('onNewSnap', 'onNewFriendRequest', 
                'saveFriendByNameToDBWithDefaults'))
            ->getMock();

        $mock->expects($this->exactly(5))
            ->method('saveFriendByNameToDBWithDefaults');

        $mock->initialize();
        $mock->startForOneCycle();
    }

    /**
     * @depends testInitializeForDB
     * @short
     */
    public function testonNewSnapForNewSnapsArraySize(){
        $mock = $this->getMockBuilder('DummyMasterBot')
            ->setConstructorArgs(Array(self::$dummyDomainEntity))
            ->setMethods(array('onNewSnap', 'onNewFriendRequest',
                'saveFriendByNameToDBWithDefaults'))
            ->getMock();

        $mock->expects($this->exactly(5))->method('onNewSnap');

        $mock->initialize();
        $mock->startForOneCycle();
    }

    /**
     * @depends testInitializeForDB
     * @short
     */
    public function testEmptyGetFriends(){
        $mock = $this->getMockBuilder('DummyMasterBot')
            ->setConstructorArgs(Array(self::$dummyDomainEntity))
            ->setMethods(array('onNewSnap', 'onNewFriendRequest',
                'saveFriendByNameToDBWithDefaults', 'getCurrentFriends'))
            ->getMock();

        $mock->expects($this->once())
            ->method('getCurrentFriends')
            ->will($this->returnValue(Array()));

        $mock->initialize();
        $mock->startForOneCycle();
    }

    /**
     * @depends testInitializeForDB
     * @short
     */
    public function testEmptyGetSnaps(){
        $mock = $this->getMockBuilder('DummyMasterBot')
            ->setConstructorArgs(Array(self::$dummyDomainEntity))
            ->setMethods(array('onNewSnap', 'onNewFriendRequest',
                'saveFriendByNameToDBWithDefaults', 'getCurrentFriends',
                'getNewSnaps'))
            ->getMock();

        $mock->expects($this->once())
            ->method('getNewSnaps')
            ->will($this->returnValue(Array()));

        $mock->initialize();
        $mock->startForOneCycle();
    }

    /**
     * @depends testInitializeForDB
     * @short
     */
    public function testEmptyGetNewFriends(){
        $mock = $this->getMockBuilder('DummyMasterBot')
            ->setConstructorArgs(Array(self::$dummyDomainEntity))
            ->setMethods(array('onNewSnap', 'onNewFriendRequest',
                'saveFriendByNameToDBWithDefaults', 'getCurrentFriends',
                'getNewSnaps', 'getNewFriends'))
            ->getMock();

        $mock->expects($this->once())
            ->method('getNewFriends')
            ->will($this->returnValue(Array()));

        $mock->initialize();
        $mock->startForOneCycle();
    }

    public function testRedis(){
        $redis = $this->invokeMethod(self::$dummyMasterBot, 
            'getRedisConnection');
       $this->assertEquals($redis->ping(), "+PONG"); 
    }

    public function testDoesEndWithMP4True(){
        $result = $this->invokeMethod(self::$dummyMasterBot, 
            'doesEndWithMP4', array('file.mp4'));
        $this->assertTrue($result);
    }

    public function testDoesEndWithMP4False(){
        $result = $this->invokeMethod(self::$dummyMasterBot, 
            'doesEndWithMP4', array('file.none'));
        $this->assertFalse($result);
    }

    public function testDoesEndWithMP4NoExtension(){
        $result = $this->invokeMethod(self::$dummyMasterBot, 
            'doesEndWithMP4', array('file'));
        $this->assertFalse($result);
    }

    public function testMakeWebmPreviewVideo(){
        $testFile = "objectivetruth_546915428790740918r";
        $simpleTestVideoPath = __DIR__.
            "/../testresources/$testFile.mp4";
        $pathToFinalWebmFile = __DIR__.
            "/../../../webmtemp/_Miguel/$testFile.webm";
        $result = $this->invokeMethod(self::$dummyMasterBot, 
            'makeWebmPreviewVideo', array($simpleTestVideoPath));
        $this->assertFileExists($pathToFinalWebmFile);
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
    protected function getEmMock()
    {   
        $emMock  = $this->getMock('\Doctrine\ORM\EntityManager',
            array('getRepository', 'getClassMetadata', 
            'persist', 'flush'), array(), '', false);
        $emMock->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue(new FakeRepository()));
        $emMock->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue((object)array('Domain' => 'Domain')));
        $emMock->expects($this->any())
            ->method('persist')
            ->will($this->returnValue(null));
        $emMock->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(null));
        return $emMock;  // it tooks 13 lines to achieve mock!
    }
}

//Implementation of MasterBot for testing
class DummyMasterBot extends MasterBot{
    protected function onNewFriendRequest($newFriends){
    }
    protected function saveFriendByNameToDBWithDefaults($newFriends){
    }
    protected function onNewSnap($snap){
    }
    protected function refreshToken(){
    }
    protected function getDefaultFriendPermission(){
        return 0;
    }
    protected function getNewFriends(){
        return Array("Alex", "Caleb", "Elias", "Thomas", "Anthony");
    }
    protected function getNewSnaps(){
        return Array("Alex", "Caleb", "Elias", "Thomas", "Anthony");
    }
    protected function getCurrentFriends(){
        return Array("Alex", "Caleb", "Elias", "Thomas", "Anthony");
    }
    public function setEntityManager($entityManager){
       $this->domainEntityManager = $entityManager; 
    }

}


?>
