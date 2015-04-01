<?php

require_once("../src/snapchat.php");
require "utils.php";
require "constants.php";

//////////// CONFIG ////////////
$username = "devtestzoom"; // Your snapchat username
//$username = $argv[1]; //Sets the username to the first argument
$password = "zoomlens29"; // Your snapchat password
//$password = $argv[2]; //Sets the password to the 2nd argument
$debug = false; // Set this to true if you want to see all outgoing requests and responses from server

$accountName = "tke"; //Accountname to use 
////////////////////////////////
//

$snapchatBot = new SnapchatBot($accountName);

//Setup variables and DB connections
$snapchatBot.initialize();



$imagePath = "objectivetruth_521045426109293831r.jpg"; // URL or local path to a media file (image or video)
$sendTo = array("objectivetruth");

$snapchat = new Snapchat($username, $debug);

//Login to Snapchat with your username and password
$snapchat->login($password);

// Get your friends in an array
$friends = $snapchat->getFriends();

echo "My friends: ";
print_r($friends);

// Send snap adding text to your image and 10 seconds
//$snapchat->send($imagePath, $sendTo, "this is a test :D", 10);

// Set a story
//$snapchat->setStory($imagePath);

// Set a story adding text to the image and 5 seconds
//$snapchat->setStory($imagePath, 5, "This is my story");


// Get snaps data (Without storing them)
//$snapchat->getSnaps();

// Automatically downloads Snaps and store it in 2nd argument dir, default is 'anon'
$snapchat->getSnaps(true, yhtestor);

// Send chat message to "username"
//$snapchat->sendMessage("username", "hello from Snap-API!");

?>
