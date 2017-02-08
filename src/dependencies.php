<?php

// register new services in containers

$container = $app->getContainer();
// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------
// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);
    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->get('request')->getUri()));
    $view->addExtension(new Twig_Extension_Debug());
    return $view;
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------
// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG));
    return $logger;
};

$container['db'] = function($c) {

    // get a db config file. rn dont need since
    // default mongo connection is to localhost
    // $config = $c->get('dbconfig');
    $config = array();

    return new DatabaseService($config);
}


// -----------------------------------------------------------------------------
// Controller factories
// -----------------------------------------------------------------------------
$container[Controller\ApiController::class] = function ($c) {
    // create new controller instance and pass the logger into it
    return new App\Controller\ApiController($c->get('logger'));
};

