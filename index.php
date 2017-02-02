<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require 'core/core.php';

$app = new \Slim\App;
//twig container
$container = $app->getContainer();
$container['view'] = function($container) {
	$view = new \Slim\Views\Twig('templates'), [
		'cache' => false //idk how to cache
	]);
	$basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
}

//without Twig
$app->get
(
	'/otherTest/{name}',
	function(Request $request, Response $response)
	{
		$name = $request->getAttribute('name');
		$response->getBody()->write("Hello, $name");

		return $response;
	}
);

//with Twig:
// $this->view invoked inside the route callback is a reference to the \Slim\Views\Twig instance returned by the view container service. The \Slim\Views\Twig instance’s render() method accepts a PSR 7 Response object as its first argument, the Twig template path as its second argument, and an array of template variables as its final argument. The render() method returns a new PSR 7 Response object whose body is the rendered Twig template.
$app->get('/test/{param}', function ($request, $response, $args) {
	$newResponse = getInventoryItems($request, $response);
    return $this->view->render($newResponse, 'template_child.html', [
        'param' => $args['param'],
        'response' => $newResponse
    ]);
})->setName('url?');



$app->run();
