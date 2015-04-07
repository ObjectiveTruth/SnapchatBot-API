<?php

require_once __DIR__ . "/src/botfactory.php";

//////////// CONFIG ////////////
$accountName = "Alex"; //Accountname to use 
////////////////////////////////

$botFactory = new BotFactory();
$mybot = $botFactory->returnBotForAccount($accountName);
$mybot->initialize();
//$mybot->startForOneCycle();


//$snapchatBot = new SnapchatBotCustom($accountName);

//Setup variables and DB connections
//$snapchatBot->initialize();


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
