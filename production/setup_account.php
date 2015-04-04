<?php
require_once "constants.php";

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
    "bot_username VARCHAR(128) NOT NULL, bot_password VARCHAR(128) NOT NULL,
        ts TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
                ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY ( " . MASTER_TABLE_ACCOUNT . " ) 
     );");

if ($argc < 2){
    echo "Error: Didn't provide any arguments\n";
    echo "Usage: php5 setup_account.php <account>\n";
    echo "<account>: name of account to be used for db and tables\n\n";
    exit(1);
}

$AccountName = $argv[1];

echo "AccountName: " , $AccountName, "\n";



createMasterDBAndTable(); //Will only creates if required

createAccountDBAndTables($AccountName); //Will only Creates if required

createNewAccountEntry($AccountName); //If the account already exists, will ask before overwrite



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

function createNewAccountEntry($AccountName){
    try {
        $conn = new PDO("mysql:host=" . SQLDBSERVERNAME, 
            SQLDBUSERNAME, SQLDBPASSWORD);
        //set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "Connected successfully\n";

        $conn->exec("USE " . MASTER_SQL_DB_NAME);

        //Check if the accountname already exists if so prompt user with overwrite
        $statement = $conn->prepare("SELECT 1 FROM " . MASTER_SQL_TABLE_NAME . " WHERE " .
            MASTER_TABLE_ACCOUNT . " = :account ;");

        $statement->bindParam(':account', $AccountName, PDO::PARAM_STR, 128);

        $statement->execute()
            or die(print_r($conn->errorInfo(), true));

        if($statement->fetchColumn()){
            echo "$AccountName already exists.\n";
            echo "Overwriting will reinitialize all the settings to defaults\n";
            echo "Ovewrite? [y/n]:";

            //if answers yes, return
            $handle = fopen ("php://stdin","r");
            $line = fgets($handle);
            if(trim($line) != 'y'){
                return;
            }
        }

        //Insert new row with AccountName with defalt values
        $statement = $conn->prepare("INSERT IGNORE INTO " . 
            MASTER_SQL_TABLE_NAME . "(" . MASTER_TABLE_ACCOUNT .
            ", " . MASTER_TABLE_BOT_TYPE . ") VALUES(:account, :bot_type)");

        $statement->execute(array("account" => $AccountName, "bot_type" => '0'))
            or die(print_r($conn->errorInfo(), true));

        echo "Created $AccountName row to Master Table Successfully\n";
        $conn = null;
    }
    catch(PDOException $e)
    {
        echo "Connection failed: \n" . $e->getMessage();
    }

}

?>
