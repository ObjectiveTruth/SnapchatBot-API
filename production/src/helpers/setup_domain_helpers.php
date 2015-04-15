<?php

function createNGNIXEntry($domainName){
    if(isNGNIXSetup() == false){
        echo " Couldn't Create NGNIXEntry for domain $domainName\n";
        return;
    }
    if(doesNGNIXServerExistForDomain($domainName)){
        echo "Server entry already exists for that domain\n";
        if(doesUserWantToOverwrite()){
            echo "Creating new server for entry $domainName\n";
            deleteSymbolicLinkIfExistsForDomain($domainName);
        }else{
            echo "NGNIX server entry NOT changed\n";
            return;
        }
    }
    echo "Create NGINX server entry?\n";

    if(doesUserAgree() == false){
        return;
    }

    if(writeNewNGNIXServerEntryForDomain($domainName)){
        echo "Successfully created Server Entry\n";
    }else{
        echo "Error: Failed to create server entry\n";
    }

    echo "Send graceful update signal to NGNIX?";
    if(doesUserAgree()){
        if(sendGracefulUpdateSignalToNGINX()){
            echo "Update Signal Successful\n";
        }else{
            echo "Error: Update signal Failed\n";
        }
    }else{
        return;
    }
}

function writeNewNGNIXServerEntryForDomain($domainName){
    global $domainObj;
    $NGNIXAvailableFile = "/etc/nginx/sites-available/" . $domainName;
    $NGNIXEnabledFile = "/etc/nginx/sites-enabled/" . $domainName;
    if($domainObj == null){
        echo "Error: No domain found, did you run createNGNIXEntry?\n";
        return false;
    }
    $portNumber = $domainObj->getPortNumber();
    $serverContents = 
    "server {"                                      . PHP_EOL .
    "   server_name $domainName.objectivetruth.ca;"    . PHP_EOL .
    "   location / {"                               . PHP_EOL . 
    "      proxy_pass    http://127.0.0.1:$portNumber/;"   . PHP_EOL . 
    "   }"                                          . PHP_EOL . 
    "}"                                             . PHP_EOL;

    if(file_put_contents($NGNIXAvailableFile, $serverContents)){
        if(symlink($NGNIXAvailableFile, $NGNIXEnabledFile)){
            return true;
        }else{
            echo "Error: Couldn't create symbolic link $NGNIXEnabledFile\n";
            return false;
        }
    }else{
        echo "Error: Couldn't creating file: $NGNIXAvailableFile\n";
        return false;
    }
}

function isNGNIXSetup(){
    $NGNIXAvailableDir = "/etc/nginx/sites-available/";
    $NGNIXEnabledDir = "/etc/nginx/sites-enabled/";
    if(file_exists($NGNIXAvailableDir) && file_exists($NGNIXEnabledDir)){
        return true;
    }else{
        echo "Error: Couldn't find NGNIX Directories\n";
        echo "$NGNIXAvailableDir and $NGNIXEnabledDir\n";
        echo "Are you sure its installed?\n";
        return false;
    }
}

function doesNGNIXServerExistForDomain($domainName){
    $NGNIXServer = "/etc/nginx/sites-available/" . $domainName;
    if(file_exists($NGNIXServer)){
        return true;
    }else{
        return false;
    }
}

function getBotTypeFromUser(){
    //Loop until valid input and return
    $trimmedline; $bot_type;
    $handle = fopen ("php://stdin","r");

    do{
        echo "BotType? (leave empty for default:0):";
        $trimmedline = trim(fgets($handle));

} while(!(is_numeric($trimmedline) || empty($trimmedline)));

if(empty($trimmedline)){
    $bot_type = 0;
}
else{
    $bot_type = intval($trimmedline);
}

return $bot_type;
}

function getBotUsernameFromUser(){
    $trimmedline; $bot_username;
    $handle = fopen ("php://stdin","r");

    echo "Bot Username? (can be empty but won't be usable):";
    $trimmedline = trim(fgets($handle));
    return $trimmedline;
}

function getBotPasswordFromUser(){
    $trimmedline; $bot_password;
    $handle = fopen ("php://stdin","r");

    echo "Bot Password? (can be empty but won't be usable):";
    $trimmedline = trim(fgets($handle));
    return $trimmedline;
}

function getDomainUsernameFromUser(){
    $trimmedline; $domain_username;
    $handle = fopen ("php://stdin","r");

    echo "Domain Username? (can be empty but won't be able to login):";
    $trimmedline = trim(fgets($handle));
    return $trimmedline;
}

function getDomainPasswordFromUser(){
    $trimmedline; $domain_password;
    $handle = fopen ("php://stdin","r");

    echo "Domain Password? (can be empty but won't be able to login):";
    $trimmedline = trim(fgets($handle));
    return $trimmedline;
}


function doesUserWantToOverwrite(){
    echo "Ovewrite? [y/n]:";

    $handle = fopen ("php://stdin","r");
    $line = fgets($handle);
    if(trim($line) == 'y' || trim($line) == 'yes'){
        return true;
    }
    else{
        return false;
    }
}

function doesUserAgree(){
    echo "[y/n]:";

    $handle = fopen ("php://stdin","r");
    $line = fgets($handle);
    if(trim($line) == 'y' || trim($line) == 'yes'){
        return true;
    }
    else{
        return false;
    }
}

function getLowestAvailablePort($customers){
    $lowestPort = 5000;
    $highestPort = 5500;
    $sortedPortNumbers = $customers
        ->findBy(array(), array('port_number' => 'ASC'));
    if(empty($sortedPortNumbers)){
        echo "Port Number: " . $lowestPort . "\n";
        return $lowestPort;
    }else{
        $j = 0;
        for($i = $lowestPort; $i < $highestPort; $i++){
            $iOffset = $i - $lowestPort +1; //to compare index to size
            if($iOffset > count($sortedPortNumbers) 
                || $i != $sortedPortNumbers[$j]->getPortNumber()){
                    echo "Port Number: " . $i . "\n";
                    return $i;
                }else{
                    $j++;
                }
        }
    }
}

function deleteSymbolicLinkIfExistsForDomain($domainName){
    $NGNIXEnabledFile = "/etc/nginx/sites-enabled/" . $domainName;
    if(file_exists($NGNIXEnabledFile)){
        return unlink($NGNIXEnabledFile);
    }else{
        return true;
    }
}

function sendGracefulUpdateSignalToNGINX(){
    $HUPSignal = 1;
    $NGNIXPIDLocation = "/run/nginx.pid";
    $NGINXPID = file_get_contents($NGNIXPIDLocation);
    if($NGINXPID === false){
        echo "Error: Couldn't locate PID for NGINX.\n";
        echo "Support to be found at $NGNIXPIDLocation\n";
        return false;
    }else{
        $NGINXPID = intval($NGINXPID);
        return posix_kill($NGINXPID, $HUPSignal);
    }
    

}

function isUserNotRoot(){
    $root_uid = 0;
    if (posix_getuid() == $root_uid){
        return false;
    } else {
        return true;
    }
}



?>
