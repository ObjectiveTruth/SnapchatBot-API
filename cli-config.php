<?php
//cli-config.php
require_once "./production/src/bootstrap.php";

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
?>
