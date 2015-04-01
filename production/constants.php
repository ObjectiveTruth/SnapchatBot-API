<?php
//Save directory in main dir. if you need to go up use "../snaps"
define('SNAPS_SAVEDIR', 'snaps');

//Master SQL DatabaseName
define("MASTER_SQL_DB_NAME", "snapchatbot_db");

//Master SQL TABLE NAME with user configs 
define("MASTER_SQL_TABLE_NAME", "MASTER_ACCOUNTS_CONFIG");

define('SQLDBSERVERNAME', "localhost");

define('SQLDBUSERNAME', "root");

define('SQLDBPASSWORD', "devtest");

define('FRIENDS_TABLE_NAME', 'friends');

define('MASTER_TABLE_BOT_TYPE', 'bot_type');

define('TABLE_ID', '_id');

define('MASTER_TABLE_ACCOUNT', 'accountname');

define('BOT_TYPE_WHITELIST', '0');
define('BOT_TYPE_BLACKLIST', '1');
define('BOT_TYPE_REFLECTOR', '2');

?>
