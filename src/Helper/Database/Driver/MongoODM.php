<?php

namespace App\Helper\Database\Driver;

// import helper class
use App\Helper\Container\ContainerHelper;


// import doctrine ODM stuff from /vendor
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;
use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Configuration;


class MongoODM {

	// document manager (check mongoODM documentation)
	protected static $dm;

	private $connectionSettings;

	/**
	 * MongoODM constructor
	 */
	public function __construct($settings) {
		$appContainer = ContainerHelper::getContainer();

		// use $this instead of self:: since connectionConfig is a 
		// non-static object. 
		// use self::object when object is static variable of class
		$this->connectionSettings = $settings;
	}

	function newConnection() {
		$connectionString = 'mongodb://';

		// LEAVE THESE COMMENTED OUT FOR LOCALHOST CONNECTION
		// $user     = $this->connectionSettings['connection']['user'];
  //       $password = $this->connectionSettings['connection']['password'];
        $dbName = $this->connectionSettings['connection']['dbname'];
        // if ($user && $password) {
        // 	$connectionString .= $user. ':' .$password .'@';
        // }

        $connectionString .= $this->connectionSettings['connection']['server'] .":".$this->connectionSettings['connection']['port'];

        if ($dbName) {
        	$connectionString .= '/'.$dbName;
        }

        $config = new Configuration();
        $config->setDefaultDB($dbName);
        $config->setProxyDir($this->connectionSettings['configuration']['ProxyDir']);
        $config->setProxyNamespace('Proxies');
        $config->setHydratorDir($this->connectionSettings['configuration']['HydratorsDir']);
        $config->setHydratorNamespace('Hydrators');
		$config->setAutoGenerateHydratorClasses(false);
        $config->setAutoGenerateProxyClasses(false);

        $config->setMetadataDriverImpl(AnnotationDriver::create($this->connectionSettings['configuration']['Models']));

        // not sure what this does. ill come back to it
        $config->setRetryConnect(true);

        // gets the model classes and does something with those annotations
        AnnotationDriver::registerAnnotationClasses();

        // $connection = new Connection();
        $connection = new Connection($connectionString, [], $config);

    	$dm = DocumentManager::create($connection, $config);
   		return $dm;
       
	}


	public function createConnection() {
		return $this->newConnection();
	}

	public function getConnection() {
		return self::$dm;
	}
}