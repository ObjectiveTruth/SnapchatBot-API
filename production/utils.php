<?php
require "constants.php";

class SnapchatBot{

    function initialize($accountName){
        try {
            $conn = new PDO("mysql:host=" . SQLDBSERVERNAME . ";dbname=" . MASTER_SQL_DB_NAME, 
                SQLDBUSERNAME, SQLDBPASSWORD);
            //set the PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully\n";
        }
        catch(PDOException $e)
        {
            echo "Connection failed: \n" . $e->getMessage();
        }
    }
}
?>
