<?php
require_once __DIR__ . "/../constants.php";
require_once __DIR__ . "/../../vendor/autoload.php";

//Bootstraps the Db Connection
use Doctrine\ORM\Tools\Setup;
use \Doctrine\ORM\EntityManager;

class ORMDBConnection {
    private $config = null;
    private $entityManager = null;

    function __construct($dbName){
        $config = new \Doctrine\DBAL\Configuration();
        // Create a simple "default" Doctrine ORM configuration for Annotations
        $metadataconfig = Setup::createAnnotationMetadataConfiguration(array(
            __DIR__."/src/schema"));
        // or if you prefer yaml or XML
        //$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
        //$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

        // database configuration parameters
        $connectionParams = array(
            'dbname' => $dbName,
            'user' => SQLDBUSERNAME,
            'password' => SQLDBPASSWORD,
            'host' => SQLDBSERVERNAME,
            'driver' => 'pdo_mysql',
        );

        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        // obtaining the entity manager
        $this->entityManager = EntityManager::create($conn, $metadataconfig);
    }
    
    public function getEntityManager(){
        return $this->entityManager;
    }
}
?>
