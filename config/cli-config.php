<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;

// replace with file to your own project bootstrap
require_once __DIR__ . '/../production/src/ormbootstrap.php';

// replace with mechanism to retrieve EntityManager in your app
$accountDBConnection = new ORMDBConnection(MASTER_SQL_DB_NAME);
$entityManager = $accountDBConnection->getEntityManager();

return ConsoleRunner::createHelperSet($entityManager);
?>
