<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
# import necessary packages from composer
require '../vendor/autoload.php';


// get settings from settings file
$settings = require '../src/settings/php';

# instantiate appplication object
$app = new \Slim\App($settings);

// set dependencies
require '../src/dependencies.php';

// set routes
require '../src/routes/php';

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