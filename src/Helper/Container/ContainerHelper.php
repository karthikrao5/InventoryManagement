<?php

namespace App\Helper\Container;

use Slim\App;
use Slim\Container;

/**
 * This class is used as a global app instance to be able to
 * grab app variables such as container etc.
 */
class ContainerHelper {

	/**
     * App instance created at index.php
	 */ 
	private static $application;

	/**
	 * Get slim container
	 * @return \Interop\Container\ContainerInterface Container
	 */
	public static function getContainer() {

		if (self::$application == null) {
            return new Container();
        }
        return self::$application->getContainer();
	}

	public static function setApplication(App $application) {
		self::$application = $application;
	}

}