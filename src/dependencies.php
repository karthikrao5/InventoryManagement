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

        // $settings['view']['twig']);
    // $view = new \Slim\Views\Twig(__DIR__.'/templates', [
    //         'cache' => false,
    //     ]);

    // Add extensions
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->request->getUri()));
    $view->addExtension(new Twig_Extension_Debug());
    return $view;
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------
// monolog
// $container['logger'] = function ($c) {
//     $settings = $c->get('settings');
//     $logger = new Monolog\Logger($settings['logger']['name']);
//     $logger->pushProcessor(new Monolog\Processor\UidProcessor());
//     $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['logger']['path'], Monolog\Logger::DEBUG));
//     return $logger;
// };
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

$container['db'] = function($c) {
    $db = new App\core\CoreService();
    return $db;
};


// -----------------------------------------------------------------------------
// Controller factories
// -----------------------------------------------------------------------------
// $container[App\Controller\ApiController::class] = function ($c) {
//     // create new controller instance and pass the logger into it
//     return new App\Controller\ApiController($c->get('logger'));
// };

$container['HomeController'] = function ($c) {
    return new \App\Controller\HomeController($c->get('view'), $c->get('logger'));
};

$container["ApiController"] = function($c) {
    return new \App\Controller\ApiController();
};

