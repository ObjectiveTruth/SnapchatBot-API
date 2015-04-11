<?php
require_once "constants.php";
require_once __DIR__ . "/src/ormbootstrap.php";
require_once __DIR__ . "/src/schema/customer.php";

//Locally Required Constants
define("FRIENDS_TABLE_SCHEMA", "
    (   username VARCHAR(128) NOT NULL, 
        permission INT NOT NULL, 
        ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
                ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY ( username ) 
     );");

define("MASTER_TABLE_SCHEMA", "
    ( ".MASTER_TABLE_ACCOUNT . "  VARCHAR(128) NOT NULL, " .
    MASTER_TABLE_BOT_TYPE . " INT NOT NULL, " .
    "port_number INT NOT NULL, " . 
    "bot_username VARCHAR(128) NOT NULL, " .
    "bot_password VARCHAR(128) NOT NULL, " .
        "ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP " .
                "ON UPDATE CURRENT_TIMESTAMP, " .
        "PRIMARY KEY ( " . MASTER_TABLE_ACCOUNT . " ) 
     );");

if ($argc < 2){
    echo "Error: Didn't provide any arguments\n";
    echo "Usage: php5 setup_account.php <account>\n";
    echo "<account>: name of account to be used for db and tables\n\n";
    exit(1);
}

$AccountName = $argv[1];
$accountObj;

echo "AccountName: " , $AccountName, "\n";

createMasterDBAndTable(); //Will only creates if required

createAccountDBAndTables($AccountName); //Will only Creates if required

createAccountEntry($AccountName); //Asks before overwriteing

if(isUserNotRoot()){
    echo "\n==You're not using sudo, NGINX won't work right==\n\n";
    exit(1);
}else{
    createNGNIXEntry($AccountName); //Checks if account already exists
}



//Functions
function createMasterDBAndTable(){

    try {
        $conn = new PDO("mysql:host=" . SQLDBSERVERNAME, 
            SQLDBUSERNAME, SQLDBPASSWORD);
        //set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully\n";

        $conn->exec("CREATE DATABASE IF NOT EXISTS " . MASTER_SQL_DB_NAME)
        or die(print_r($conn->errorInfo(), true));
        echo "Created Database " . MASTER_SQL_DB_NAME . " or already exists\n";

        $conn->exec("USE " . MASTER_SQL_DB_NAME);
        $sqlreply = $conn->exec("CREATE TABLE IF NOT EXISTS " . MASTER_SQL_TABLE_NAME . 
            " " . MASTER_TABLE_SCHEMA);
        if($sqlreply === false){
            die(print_r($conn->errorInfo(), true));
        }
        echo "Created " . MASTER_SQL_TABLE_NAME . " Table Successfully\n";

        $conn = null;
    }
    catch(PDOException $e){
        echo "Connection failed: \n" . $e->getMessage();
    }
}

function createAccountDBAndTables($AccountName){

    try {
        $conn = new PDO("mysql:host=" . SQLDBSERVERNAME, 
            SQLDBUSERNAME, SQLDBPASSWORD);
        //set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully\n";

        $conn->exec("CREATE DATABASE IF NOT EXISTS " . $AccountName)
        or die(print_r($conn->errorInfo(), true));
        echo "Created $AccountName Database or already exists\n";

        $conn->exec("USE " . $AccountName);
        $sqlreply = $conn->exec("CREATE TABLE IF NOT EXISTS " . FRIENDS_TABLE_NAME . 
            " " . FRIENDS_TABLE_SCHEMA);
        if($sqlreply === false){
            die(print_r($conn->errorInfo(), true));
        }
        echo "Created Friends Table Successfully or it already existed\n";

        $conn = null;
    }
    catch(PDOException $e)
    {
        echo "Connection failed: \n" . $e->getMessage();
    }
}

function createAccountEntry($AccountName){
        global $accountObj;
        $botUserType;
        $accountDBConnection = new ORMDBConnection(MASTER_SQL_DB_NAME);
        $accountEntityManager = $accountDBConnection->getEntityManager();
        $customers = $accountEntityManager->getRepository('Customer');
        $customer = $customers->find($AccountName);

        if($customer == null){

            $customer = new Customer($AccountName, getBotTypeFromUser());
            $customer->setPortNumber(getLowestAvailablePort($customers));

        }else{
            echo "$AccountName already exists.\n";
            echo "Overwriting will reinitialize all the settings\n";
            if(doesUserWantToOverwrite()){
                $customer->setBotType(getBotTypeFromUser());
                echo "Using previous port: " . $customer->getPortNumber();
            }
            else{
                $accountObj = $customer;
                return;
            }
        }


        $customer->setBotUsername(getBotUsernameFromUser());
        $customer->setBotPassword(getBotPasswordFromUser());
        $accountObj = $customer;

        $accountEntityManager->persist($customer);
        $accountEntityManager->flush();
        echo "\nCreated Customer Object:\n";
        print_r($customer);
}

function createNGNIXEntry($accountName){
    if(isNGNIXSetup() == false){
        echo " Couldn't Create NGNIXEntry for account $accountName\n";
        return;
    }
    if(doesNGNIXServerExistForAccount($accountName)){
        echo "Server entry already exists for that account\n";
        if(doesUserWantToOverwrite()){
            echo "Creating new server for entry $accountName\n";
            deleteSymbolicLinkIfExistsForAccount($accountName);
        }else{
            echo "NGNIX server entry NOT changed\n";
            return;
        }
    }

    if(writeNewNGNIXServerEntryForAccount($accountName)){
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

function writeNewNGNIXServerEntryForAccount($accountName){
    global $accountObj;
    $NGNIXAvailableFile = "/etc/nginx/sites-available/" . $accountName;
    $NGNIXEnabledFile = "/etc/nginx/sites-enabled/" . $accountName;
    if($accountObj == null){
        echo "Error: No account found, did you run createNGNIXEntry?\n";
        return false;
    }
    $portNumber = $accountObj->getPortNumber();
    $serverContents = 
    "server {"                                      . PHP_EOL .
    "   server_name $accountName.objectivetruth.ca;"    . PHP_EOL .
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

function doesNGNIXServerExistForAccount($accountName){
    $NGNIXServer = "/etc/nginx/sites-available/" . $accountName;
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
    //Loop until valid input and return
    $trimmedline; $bot_username;
    $handle = fopen ("php://stdin","r");

    echo "Bot Username? (can be empty but won't be usable):";
    $trimmedline = trim(fgets($handle));
    return $trimmedline;
}

function getBotPasswordFromUser(){
    //Loop until valid input and return
    $trimmedline; $bot_password;
    $handle = fopen ("php://stdin","r");

    echo "Bot Password? (can be empty but won't be usable):";
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

function deleteSymbolicLinkIfExistsForAccount($accountName){
    $NGNIXEnabledFile = "/etc/nginx/sites-enabled/" . $accountName;
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
