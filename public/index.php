<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

require './core/core.php';

//twig container
$container = $app->getContainer();
$container['view'] = function($container) {
	$view = new \Slim\Views\Twig('templates', [
		'cache' => false //idk how to cache
	]);
	$basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

//with Twig:
// $this->view invoked inside the route callback is a reference to the \Slim\Views\Twig instance returned by the view container service. The \Slim\Views\Twig instance’s render() method accepts a PSR 7 Response object as its first argument, the Twig template path as its second argument, and an array of template variables as its final argument. The render() method returns a new PSR 7 Response object whose body is the rendered Twig template.
$app->get('/test/{param}', function ($request, $response, $args) {

	$inv = json_decode(db_getInventory(), true);
    return $this->view->render($response, 'template_child.html', [
        'param' => $args['param'],
        'data' => $inv
    ]);
})->setName('url?');



$app->run();
