<?php
require_once "constants.php";
require_once __DIR__ . "/src/ormbootstrap.php";
require_once __DIR__ . "/src/schema/domain.php";
require_once __DIR__ . "/src/schema/user.php";
require_once __DIR__ . "/src/helpers/setup_domain_helpers.php";

//Locally Required Constants
define('FRIENDS_TABLE_SCHEMA', 
    "(username VARCHAR(255) NOT NULL, ".
    "permission INT NOT NULL, ".
    "ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, " .
    "PRIMARY KEY(username)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ".
    "ENGINE = InnoDB;");

define('USERS_TABLE_SCHEMA',
    "(username VARCHAR(255) NOT NULL, ".
    "password VARCHAR(255) NOT NULL, ".
    "permission INT NOT NULL, ".
    "reset_password_token VARCHAR(256), ".
    "reset_password_expire DATETIME, ".
    "ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, " .
    "PRIMARY KEY(username)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ".
    "ENGINE = InnoDB;");

define("DOMAINS_TABLE_SCHEMA", "
    ( ".DOMAINNAME . "  VARCHAR(255) NOT NULL, " .
    MASTER_TABLE_BOT_TYPE . " INT NOT NULL, " .
    "port_number INT NOT NULL, " . 
    "bot_username VARCHAR(128) NOT NULL, " .
    "bot_password VARCHAR(128) NOT NULL, " .
    "ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP " .
                "ON UPDATE CURRENT_TIMESTAMP, " .
        "PRIMARY KEY ( " . DOMAINNAME . " ) 
     );");

if ($argc < 2){
    echo "Error: Didn't provide any arguments\n";
    echo "Usage: php5 setup_account.php <account>\n";
    echo "<account>: name of account to be used for db and tables\n\n";
    exit(1);
}

$domainName = $argv[1];
$domainObj;

echo "DomainName: " , $domainName, "\n";

createCustomersDBAndEmptyTable(); //Will only creates if required

createAccountDBAndEmptyTables($domainName); //User's own DB for friends etc..

//showUsersForDomain($domainName);

if(doesUserWantToCreateAUser()){
    interactivelyCreateNewUsersForDomain($domainName);
}

createDomainEntry($domainName); //Asks before overwriteing

createSQLReadOnlyUserForFrontEnd($domainName);

if(isUserNotRoot()){
    echo "\n==You're not using sudo, NGINX won't work right==\n\n";
    exit(1);
}else{
    createNGNIXEntry($domainName); //Checks if account already exists
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
        $sqlreply = $conn->exec("CREATE TABLE IF NOT EXISTS " . DOMAINS_TABLE_NAME . 
            " " . DOMAINS_TABLE_SCHEMA);
        if($sqlreply === false){
            die(print_r($conn->errorInfo(), true));
        }
        echo "Created " . DOMAINS_TABLE_NAME . " Table Successfully\n";

        $conn = null;
    }
    catch(PDOException $e){
        echo "Connection failed: \n" . $e->getMessage();
    }
}

function createAccountDBAndEmptyTables($domainName){

    try {
        $conn = new PDO("mysql:host=" . SQLDBSERVERNAME, 
            SQLDBUSERNAME, SQLDBPASSWORD);
        //set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully\n";

        $conn->exec("CREATE DATABASE IF NOT EXISTS " . $domainName)
        or die(print_r($conn->errorInfo(), true));
        echo "Created $domainName Database or already exists\n";

        $conn->exec("USE " . $domainName);
        echo "Creating Friends Table if it doesn't exist\n";
        $sqlreply = $conn->exec("CREATE TABLE IF NOT EXISTS " .
            FRIENDS_TABLE_NAME .
            " " . FRIENDS_TABLE_SCHEMA);
        if($sqlreply === false){
            die(print_r($conn->errorInfo(), true));
        }

        echo "Creating Users Table if it doesn't exist\n";
        $sqlreply = $conn->exec("CREATE TABLE IF NOT EXISTS " .
            USERS_TABLE_NAME .
            " " . USERS_TABLE_SCHEMA);
        if($sqlreply === false){
            die(print_r($conn->errorInfo(), true));
        }

        $conn = null;
    }
    catch(PDOException $e)
    {
        echo "Connection failed: \n" . $e->getMessage();
    }
}

function createDomainEntry($domainName){
        global $domainObj;
        $botUserType;
        $accountDBConnection = new ORMDBConnection(MASTER_SQL_DB_NAME);
        $accountEntityManager = $accountDBConnection->getEntityManager();
        $domains = $accountEntityManager->getRepository('Domain');
        $domain = $domains->find($domainName);

        if($domain == null){

            $domain = new Domain($domainName, getBotTypeFromUser());
            $domain->setPortNumber(getLowestAvailablePort($domains));

        }else{
            echo "Domain: $domainName already exists.\n";
            echo "Overwriting will reinitialize all the settings\n";
            if(doesUserWantToOverwrite()){
                $domain->setBotType(getBotTypeFromUser());
                echo "Using previous port: " . $domain->getPortNumber().PHP_EOL;
            }
            else{
                $domainObj = $domain;
                return;
            }
        }


        $domain->setBotUsername(getBotUsernameFromUser());
        $domain->setBotPassword(getBotPasswordFromUser());
        $domainObj = $domain;

        $accountEntityManager->persist($domain);
        $accountEntityManager->flush();
        echo "\nCreated Domain Object:\n";
        print_r($domain);
}

function createSQLReadOnlyUserForFrontEnd($domainName){
    echo "Creating SQL Read Only User named $domainName".PHP_EOL;
    try {
        $conn = new PDO("mysql:host=" . SQLDBSERVERNAME, 
            SQLDBUSERNAME, SQLDBPASSWORD);
        //set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sqlreply = $conn->exec("GRANT SELECT, UPDATE on `$domainName`.* to ".
            "'$domainName'@'127.0.0.1' IDENTIFIED BY 'ironhorse'");

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
