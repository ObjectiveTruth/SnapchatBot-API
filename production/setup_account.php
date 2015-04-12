<?php
require_once "constants.php";
require_once __DIR__ . "/src/ormbootstrap.php";
require_once __DIR__ . "/src/schema/customer.php";
require_once __DIR__ . "/src/helpers/setup_account_helpers.php";

//Locally Required Constants
define('FRIENDS_TABLE_SCHEMA', 
    "(username VARCHAR(255) NOT NULL, ".
    "permission INT NOT NULL, ".
    "ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, " .
    "PRIMARY KEY(username)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ".
    "ENGINE = InnoDB;");

define("MASTER_TABLE_SCHEMA", "
    ( ".MASTER_TABLE_ACCOUNT . "  VARCHAR(255) NOT NULL, " .
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

createCustomersDBAndEmptyTable(); //Will only creates if required

createAccountDBAndEmptyTables($AccountName); //User's own DB for friends etc..

createCustomerEntry($AccountName); //Asks before overwriteing

createSQLReadOnlyUserForFrontEnd($AccountName);

if(isUserNotRoot()){
    echo "\n==You're not using sudo, NGINX won't work right==\n\n";
    exit(1);
}else{
    createNGNIXEntry($AccountName); //Checks if account already exists
}



//Major Functions (helpers in /src/helpers/setup_account_helpers.php)
function createCustomersDBAndEmptyTable(){

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

function createAccountDBAndEmptyTables($AccountName){

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

function createCustomerEntry($AccountName){
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
                echo "Using previous port: " . $customer->getPortNumber().PHP_EOL;
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

function createSQLReadOnlyUserForFrontEnd($AccountName){
    echo "Creating SQL Read Only User named $AccountName".PHP_EOL;
    try {
        $conn = new PDO("mysql:host=" . SQLDBSERVERNAME, 
            SQLDBUSERNAME, SQLDBPASSWORD);
        //set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sqlreply = $conn->exec("GRANT SELECT, UPDATE on `$AccountName`.* to ".
            "'$AccountName'@'127.0.0.1' IDENTIFIED BY 'ironhorse'");

        if($sqlreply === false){
            die(print_r($conn->errorInfo(), true));
        }

        echo "Successfully created ReadOnly user with password 'ironhorse'".PHP_EOL;


        $conn = null;
    }
    catch(PDOException $e)
    {
        echo "Connection failed: \n" . $e->getMessage();
    }

}

?>
