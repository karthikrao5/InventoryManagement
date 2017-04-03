<?php

date_default_timezone_set('America/New_York');

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
    $view = new Slim\Views\Twig($settings['view']['template_path'], $settings['view']['twig']);
    $view->addExtension(new Slim\Views\TwigExtension($c->get('router'), $c->request->getUri()));
    // $view->addExtension(new Twig_Extension_Debug());
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

// flash messages
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

$container['dm'] = function($c) {
    return App\Helper\Database\DatabaseHelper::getConnection();
};

/**
 *  This is the repository manager. you can set the repository with
 *  setRepo() and query the collection.
 */
$container['rm'] = function($c) {
    return new App\Models\RepositoryManager(App\Helper\Container\ContainerHelper::getContainer());
};



// -----------------------------------------------------------------------------
// Controller factories
// -----------------------------------------------------------------------------
// $container[App\Controller\ApiController::class] = function ($c) {
//     // create new controller instance and pass the logger into it
//     return new App\Controller\ApiController($c->get('logger'));
// };

$container['ViewController'] = function ($c) {
    return new \App\Controller\ViewController(App\Helper\Container\ContainerHelper::getContainer());
};

$container["EquipmentController"] = function ($c) {
    return new App\Controller\EquipmentController(App\Helper\Container\ContainerHelper::getContainer());
};

$container['EquipmentTypeController'] = function ($c) {
    return new App\Controller\EquipmentTypeController(App\Helper\Container\ContainerHelper::getContainer());
};

// $container["DummyController"] = function($c) {
//     return new \App\Controller\DummyController($c->get('db'));
// };


// -----------------------------------------------------------------------------
// Validators factories
// -----------------------------------------------------------------------------
$container['EquipmentValidator'] = function ($c) {
    return new App\Validators\EquipmentValidator(App\Helper\Container\ContainerHelper::getContainer());
};

$container['EquipmentTypeValidator'] = function ($c) {
    return new App\Validators\EquipmentTypeValidator(App\Helper\Container\ContainerHelper::getContainer());
};

$container["core"] = function($c) {
    return new App\Core\CoreService();
};


