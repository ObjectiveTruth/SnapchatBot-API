<?php

require_once __DIR__ . "/src/botfactory.php";

if($argc < 2){
    echo "Error: Didn't provide any arguments\n";
    echo "Usage: php5 snap_master.php <account>\n";
    echo "<account>: name of account to be used for db and tables\n\n";
    exit(1);
}

$accountName = $argv[1];

echo "Using Account: $accountName\n";

$botFactory = new BotFactory();
$mybot = $botFactory->returnBotForAccount($accountName);
$mybot->initialize();
$mybot->start();
?>
