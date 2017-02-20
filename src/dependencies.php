<?php

// register new services in containers
// If a class is in a container, whenever slim sees the Controller name being
// accessed, it will simply call the class function. (google "callable resolver slim3")

$container = $app->getContainer();
// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------
// Twig
$container['view'] = function ($c) {
    $settings = $c->get('settings');
    $view = new Slim\Views\Twig($settings['view']['template_path']);
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->request->getUri()));
    $view->addExtension(new Twig_Extension_Debug());
    return $view;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings');
    $logger = new \Monolog\Logger($settings['logger']['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['logger']['path'], \Monolog\Logger::DEBUG));
    return $logger;
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

$container['dm'] = function($c) {
    return App\Helper\Database\DatabaseHelper::getConnection();
};



// -----------------------------------------------------------------------------
// Controller factories
// -----------------------------------------------------------------------------
// $container[App\Controller\ApiController::class] = function ($c) {
//     // create new controller instance and pass the logger into it
//     return new App\Controller\ApiController($c->get('logger'));
// };

$container['HomeController'] = function ($c) {
    return new \App\Controller\HomeController($c->get('view'));
};

$container["ApiController"] = function($c) {
    return new App\Controller\ApiController(App\Helper\Container\ContainerHelper::getContainer());
};

// $container["DummyController"] = function($c) {
//     return new \App\Controller\DummyController($c->get('db'));
// };

