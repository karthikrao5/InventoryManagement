<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// use above for autoloader for ODM since you use $loader for creating documents
require __DIR__ . '/../vendor/autoload.php';


session_start();

// get settings from settings file
$settings = require __DIR__ . '/../src/settings.php';

// instantiate appplication object
$app = new \Slim\App($settings);

// set dependencies
require __DIR__ . '/../src/dependencies.php';

// set routes
require __DIR__ . '/../src/routes.php';

// add app instance to AppHelper
App\Helper\App\ContainerHelper::setApplication($app);


// slim collection provides a common interface for collections of data
$container['dbsettings'] = new \Slim\Collection(require __DIR__ . '/../src/dbconfig.php');

$settingsDatabase = $app->getContainer()->get('dbsettings');

// if db is not on, create connection
if ($settingsDatabase['boot-database']) {
    \App\Helper\Database\DatabaseHelper::getConnection();
}

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