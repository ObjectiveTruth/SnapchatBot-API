<?php
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once __DIR__ . "/../../src/schema/customer.php";
require_once __DIR__ . "/../ormbootstrap.php";
require_once __DIR__ . "/../../src/schema/friend.php";
require_once __DIR__ . "/../../../vendor/autoload.php";

abstract class MasterBot{
    const DEBUG = false;
    const PERMISSION_CAN_POST = 0;
    const PERMISSION_CANNOT_POST = 1;
    private $isInitialized = false;
    private $customerEntity;
    private $snapchat_engine;
    private $redis_client;
    private $accountEntityManager;
    protected $logger;

    abstract protected function onNewFriendRequest($newFriend);
    abstract protected function onNewSnap($snap);
    abstract protected function getDefaultFriendPermission();

    function __construct(Customer $customerEntity){
        $this->customerEntity = $customerEntity;
    }

    function initialize(){
        $botUsername = $this->customerEntity->getBotUsername();
        $accountName = $this->customerEntity->getAccountName();

        $this->startLogger();

        $this->snapchat_engine = new Snapchat($botUsername, self::DEBUG);
        $ORMDBConnection = new ORMDBConnection($accountName);
        $this->accountEntityManager = $ORMDBConnection->getEntityManager();
        $this->isInitialized = true;
    }

    function start(){
        $this->startWithIntervalInSeconds(30);
    }

    function startForOneCycle(){
        $this->startWithIntervalInSeconds(20, true);
        return true;
    }

    function startWithIntervalInSeconds($Interval, $runOnce = false){
        if(!$this->isInitialized){
            throw new Exception('Must call .initialize() first');};

        $botPassword = $this->customerEntity->getBotPassword();

        $isFirstCycle = true;

        while(true){
            //Must be called before the rest
            $this->refreshToken();

            if($isFirstCycle){
                $isFirstCycle = false;
                $currentFriends = $this->getCurrentFriends();
                if(count($currentFriends) > 0){
                    foreach ($currentFriends as $friend){
                        $this->saveFriendByNameToDBWithDefaults($friend);
                    }
                }
            }

            $newFriends = $this->getNewFriends();

            if(count($newFriends) > 0){

                foreach ($newFriends as $friend){
                    if(!$this->doesFriendNameExist($friend)){
                        $this->onNewFriendRequest($friend);
                    }
                }
            }

            $newSnapObjs = $this->getNewSnaps();
            if(count($newSnapObjs) > 0){

                foreach ($newSnapObjs as $snapObj){

                    $this->onNewSnap($snapObj);
                }
            }

            $this->onEndOfCycle();

            if($runOnce){break;}

            sleep($Interval);
        }
    }

    protected function refreshToken(){
        $botPassword = $this->customerEntity->getBotPassword();
        $this->snapchat_engine->login($botPassword);
    }

    protected function getNewFriends(){
        //Normalize for possible false value, if false return an empty array
        $snapchat_engine = $this->snapchat_engine;
        $thisCouldBeFalse = $snapchat_engine->getAddedFriends();
        if($thisCouldBeFalse === false){$thisCouldBeFalse = Array();}
        return $thisCouldBeFalse;
    }

    protected function getCurrentFriends(){
        $friendslist = $snapchat_engine = $this->snapchat_engine
            ->getFriends();
        if($friendslist === false){$friendslist = Array();}
        return $friendslist;
    }

    protected function getNewSnaps(){
        $accountName = $this->customerEntity->getAccountName();
        $newSnaps = $this->snapchat_engine->getSnaps(true, $accountName);
        //Returns false if something went wrong with the get request to snapchat
        if($newSnaps === false){$newSnaps = Array();}
        return $newSnaps;
    }

    protected function addFriendByName($friendName){
        $snapchat_engine = $this->snapchat_engine
            ->addFriend($friendName);
    }
    
    protected function markSnapIdAsViewed($id, $time=1){
        $snapchat_engine = $this->snapchat_engine
            ->markSnapViewed($id, $time);
    }

    /*
     * @return bool 
     *   True if successful, False is unsuccessful
     */ 
    protected function postSnapToStoryByFilename($snapFileName){
        return $snapchat_engine = $this->snapchat_engine
            ->setStory($snapFileName);
    }

    protected function doesFriendNameExist($friendName){
        $accountEntityManager = $this->getAccountEntityManager();
        $friend = $accountEntityManager->find("Friend", $friendName);
        if($friend == null){
            return false;
        }else{
            return true;
        }
    }

    protected function saveFriendByNameToDBWithDefaults($newFriendName){
        //Only adds if entry doesn't already exist
        $accountEntityManager = $this->getAccountEntityManager();
        if(!$this->doesFriendNameExist($newFriendName)){

            $friend = new Friend($newFriendName, 
                $this->getDefaultFriendPermission());
            $accountEntityManager->persist($friend);
            $accountEntityManager->flush();
        }
    }

    protected function getPermissionForFriendByUsername($friendName){
        $accountEntityManager = $this->getAccountEntityManager();
        return $accountEntityManager->find("Friend", $friendName)
            ->getPermission();
    }

    protected function getAccountName(){
        return $this->customerEntity->getAccountName();
    }

    private function getRedisConnection(){
        if($this->redis_client == null){
            $this->redis_client = new Redis();
        }
        $this->redis_client->pconnect('127.0.0.1');
        return $this->redis_client;
    }

    protected function addFilenameToPendingApprovalList($filename){
        $pendingApprovalListName = $this->getPendingApprovalListName();

        $redis = $this->getRedisConnection();
        $redis->lPush($pendingApprovalListName,
            $filename);
        $redis->save();
    }

    protected function getPendingApprovalListName(){
        $accountName = $this->getAccountName();
        return "$accountName-pending_approval";
    }

    protected function getPendingPostListName(){
        $accountName = $this->getAccountName();
        return "$accountName-pending_post";
    }

    function getCustomerEntity(){
        return $this->customerEntity;
    }

    protected function peekPendingPostSnap(){
        $lastElementIndex = -1; //+1 for first element
        $redis = $this->getRedisConnection();
        $pendingPostListName = $this->getPendingPostListName();
        return $redis->lGet($pendingPostListName, $lastElementIndex);
    }

    protected function popPendingPostSnap(){
        $redis = $this->getRedisConnection();
        $pendingPostListName = $this->getPendingPostListName();
        return $redis->rPop($pendingPostListName);
    }

    protected function getAccountEntityManager(){
        return $this->accountEntityManager;

    }

    protected function onEndOfCycle(){
    }

    protected function startLogger(){
        $logger = new Logger('main');
        $this->logger = $logger;
        $logFileName = __DIR__.'/../../logs/'.$this->getAccountName().'_php.log';
        //writes log files
        $logger->pushHandler(new StreamHandler($logFileName, 
            Logger::DEBUG));
        //prints to stdout
        $logger->pushHandler(new StreamHandler('php://stdout', 
            Logger::INFO));
    }
}
?>
