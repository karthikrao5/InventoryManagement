<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use Doctrine\MongoDB\Connection;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;



# import necessary packages from composer
if (! file_exists($file= __DIR__.'/../vendor/autoload.php')) {
	throw new RuntimeException('Install dependencies to run this script.');
}

$loader = require_once $file
$loader->add('/../src/Models', __DIR__);

$connection = new Connection();
$config = new Configuration();
$config->setProxyDir(__DIR__.'/../src/Proxies');
$config->setProxyNamespace('Proxies');
$config->setHydratorDir(__DIR__.'/../src/Hydrators');
$config->setHydratorNamespace('Hydrators');
$config->setDefaultDB('doctrine_odm')
;
$config->setMetadataDriverImpl(AnnotationDriver::create(__DIR__.'/../src/Models'));

AnnotationDriver::registerAnnotationClasses();
$dm = DocumentManager::create($connection, $config);

// use above for autoloader for ODM since you use $loader for creating documents
// require __DIR__ . '/../vendor/autoload.php';


session_start();

// get settings from settings file
$settings = require __DIR__ . '/../src/settings.php';

// instantiate appplication object
$app = new \Slim\App($settings);

// set dependencies
require __DIR__ . '/../src/dependencies.php';

// set routes
require __DIR__ . '/../src/routes.php';

// test route to check if app is running
$app->get('/hello/{name}', function (Request $request, Response $response) {
	
    $name = $request->getAttribute('name');
    $data = array('name' => $name, 'message' => 'hello there.');
	$newResponse = $response->withJson($data);
    return $newResponse;
});
// # import REST APIs
// require 'core/core.php';


# start application
$app->run();