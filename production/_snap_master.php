<?php

require_once __DIR__ . "/src/bootstrap.php";
require_once __DIR__ . "/src/schema/friends.php";

require_once __DIR__ . "/../src/snapchat.php";
require_once "utils.php";
require_once "constants.php";

//////////// CONFIG ////////////
$username = "devtestzoom"; // Your snapchat username
//$username = $argv[1]; //Sets the username to the first argument
$password = "zoomlens29"; // Your snapchat password
//$password = $argv[2]; //Sets the password to the 2nd argument
$debug = false; // Set this to true if you want to see all outgoing requests and responses from server

$accountName = "Jason"; //Accountname to use 
////////////////////////////////


//$snapchatBot = new SnapchatBotCustom($accountName);

//Setup variables and DB connections
//$snapchatBot->initialize();

//Get a connection to the SQL DB and entity manager for ORM
$accountDBConnection = new ORMDBConnection($accountName);
$accountEntityManager = $accountDBConnection->getEntityManager();

//$friend = new Friend();
//$friend->setUsername("Caleb");
//$friend->setPermission(14);

//$entityManager->persist($friend);
//$entityManager->flush();


//$imagePath = "objectivetruth_521045426109293831r.jpg"; // URL or local path to a media file (image or video)
//$sendTo = array("objectivetruth");

//$snapchat = new Snapchat($username, $debug);

//Login to Snapchat with your username and password
//$snapchat->login($password);

// Get your friends in an array
//$friends = $snapchat->getFriends();

//echo "My friends: ";
//print_r($friends);
$productRepository = $accountEntityManager->getRepository('Friend');
$oldFriends = $productRepository->findAll();
$oldFriendsStringArray = Array();

if($oldFriends != null){
    foreach ($oldFriends as $oldFriend) {
        array_push($oldFriendsStringArray, $oldFriend->getName());
    }
}
print_r($oldFriendsStringArray);

$freshFriends = array("Caleb", "Alex", "Jason", "Thomas");

$result = array_diff($freshFriends, $oldFriendsStringArray);

foreach ($result as $newFriendEntry){
    $friendEntity = new Friend();
    $friend->setUsername($newFriendEntry);
    $friend->setPermission(14);
    $entityManager->persist($friend);
}
$entityManager->flush();
print_r($result);

// Send snap adding text to your image and 10 seconds
//$snapchat->send($imagePath, $sendTo, "this is a test :D", 10);

// Set a story
//$snapchat->setStory($imagePath);

// Set a story adding text to the image and 5 seconds
//$snapchat->setStory($imagePath, 5, "This is my story");


// Get snaps data (Without storing them)
//$snapchat->getSnaps();

// Automatically downloads Snaps and store it in 2nd argument dir, default is 'anon'
//$snapchat->getSnaps(true, yhtestor);

// Send chat message to "username"
//$snapchat->sendMessage("username", "hello from Snap-API!");

?>
