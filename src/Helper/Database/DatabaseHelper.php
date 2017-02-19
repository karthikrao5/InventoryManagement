<?php

namespace App\Helper\Database;

use App\Helper\Container\ContainerHelper;
use Interop\Container\ContainerInterface;


/**
 *	Class to create and check connection to database
 */
class DatabaseHelper {

	/**
	 * Connection variable to db
	 */
	private static $connection;

	/**
	 * Return/Create a db ODM connection. THIS IS AN ACTUAL GETTER FUNC
	 * FABCONNECTION ACTUALLY MAKES THE ODM CONNECTION FROM App\Heloer\Database\Driver\MongoODM::class
	 */
	public static function getConnection() {
		if(!is_null(self::$connection)) {
			return self::$connection;
		}

		// gets the slim app's container instance from the ContainerHelper.php file
		$appContainer = ContainerHelper::getContainer();

		// get the db settings collection 
		$dbsettings = $appContainer->get('dbsettings');

		// get db driver, aka the MongoODM class reference
		$ODMConfig = $dbsettings->get('doctrine-odm');

		// get \App\Helper\Database\Driver\MongoODM::class
		$odmClass = $ODMConfig['helper'];

		// create ODM instance and pass db config informaiton for doctrine
		// constructor for MongoODM in src/Helper/Database/Driver
		$dbInstance = new $odmClass($ODMConfig);

		// createCOnnection in MongoODM.php
		self::$connection = $dbInstance->createConnection();

		return self::$connection;
	}

	public static function destroyConnection() {
		self::$connection = null;
	}
}